<?php

$use_white_background = true;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

global $db;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, AccessLevel, ASANumber, ASAPrimary, ASACategory, ASAPaid, ClubMember, ClubPaid, ClubCategory FROM users WHERE UserID = ?");
$userInfo->execute([$id]);

$qualifications;
try {
  $qualifications = $db->prepare("SELECT `Name`, Info, `From`, `To` FROM qualifications INNER JOIN qualificationsAvailable ON qualifications.Qualification = qualificationsAvailable.ID WHERE UserID = ? ORDER BY `Name` ASC");
  $qualifications->execute([$id]);
} catch (Exception $e) {
  // Do nothing just handle table not existing
}

$logins = $db->prepare("SELECT `Time`, `IPAddress`, Browser, `Platform`, `GeoLocation` FROM userLogins WHERE UserID = ? ORDER BY `Time` DESC LIMIT 1");
$logins->execute([$id]);
$loginInfo = $logins->fetch(PDO::FETCH_ASSOC);

$info = $userInfo->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

$par = $coa = $com = $gal = $adm = "";
if ($info['AccessLevel'] == "Coach") {
  $coa = "selected";
} else if ($info['AccessLevel'] == "Committee") {
  $com = "selected";
} else if ($info['AccessLevel'] == "Galas") {
  $gal = "selected";
} else if ($info['AccessLevel'] == "Admin") {
  $adm = "selected";
} else {
  $par = "selected";
}

$swimmers = null;
if ($info['AccessLevel'] == "Parent") {
  $swimmers = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, SquadName squad, SquadFee fee, ClubPays exempt FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE members.UserID = ?");
  $swimmers->execute([$id]);
}

// Is this parent also a swimmer member?
$swimmerToo = $db->prepare("SELECT MemberID FROM members WHERE UserID = ? AND ASANumber = ?");
$swimmerToo->execute([
  $id,
  $info['ASANumber']
]);
$swimmerToo = $swimmerToo->fetchColumn();

$bankName = $bank = $has_logo = $logo_path = null;
if (userHasMandates($id)) {
  $bankName = mb_strtoupper(bankDetails($id, "account_holder_name"));
  if ($bankName != "UNKNOWN") {
    $bankName = $bankName . ', ';
  } else {
    $bankName = null;
  }
  $bank = mb_strtoupper(bankDetails($id, "bank_name"));
  $logo_path = getBankLogo($bank);
}

$userObj = new \User($id, $db, false);
$json = $userObj->getUserOption('MAIN_ADDRESS');
$addr = null;
if ($json != null) {
  $addr = json_decode($json);
}

$number = null;
try {
  $number = PhoneNumber::parse($info['Mobile']);
}
catch (PhoneNumberParseException $e) {
  $number = false;
}

$accessLevel = 'Team Manager';
if ($info['AccessLevel'] != 'Committee') {
  $accessLevel = $info['AccessLevel'];
}

$pagetitle = htmlspecialchars($info['Forename'] . ' ' . $info['Surname']) . " Information";
$title = null;
include BASE_PATH . "views/header.php";
?>
<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("users")?>">Users</a></li>
      <li class="breadcrumb-item active" aria-current="page">
        <?=htmlspecialchars(mb_substr($info["Forename"], 0, 1, 'utf-8') . mb_substr($info["Surname"], 0, 1, 'utf-8'))?>
      </li>
    </ol>
  </nav>

  <?php if (isset($_SESSION['User-Update-Email-Error']) && $_SESSION['User-Update-Email-Error']) { ?>
  <div class="alert alert-danger">
    <strong>We were not able to update the user's email address because it was not valid</strong>
  </div>
  <?php unset($_SESSION['User-Update-Email-Error']); } ?>

  <?php if (isset($_SESSION['User-Update-Email-Success']) && $_SESSION['User-Update-Email-Success']) { ?>
  <div class="alert alert-success">
    <strong>We've updated the user's email address</strong>
  </div>
  <?php unset($_SESSION['User-Update-Email-Success']); } ?>

  <div class="row mb-3">
    <div class="col-sm-9 col-md-10 col-lg-11">
      <h1 class="mb-0">
        <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?>
        <small><?=htmlspecialchars($accessLevel)?></small>
      </h1>
      <?php if ($_SESSION['AccessLevel'] == 'Admin') { ?>
      <div class="mb-3 d-md-none"></div>
      <?php } ?>
    </div>
    <?php if ($_SESSION['AccessLevel'] == 'Admin') { ?>
    <div class="col text-sm-right">
      <p class="mb-0">
        <a href="<?=htmlspecialchars(autoUrl("users/" . $id . "/edit"))?>" class="btn btn-success">
          Edit
        </a>
      </p>
    </div>
    <?php } ?>
  </div>

  <div class="mb-4">
    <h2>
      Basic Information
    </h2>
    <p class="lead">
      Basic contact details.
    </p>

    <div class="row">
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Name</h3>
        <p><?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?></p>
      </div>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Email</h3>
        <p class="text-truncate"><a
            href="mailto:<?=htmlspecialchars($info['EmailAddress'])?>"><?=htmlspecialchars($info['EmailAddress'])?></a>
        </p>
      </div>
      <?php if ($number !== false) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Phone</h3>
        <p><a
            href="<?=htmlspecialchars($number->format(PhoneNumberFormat::RFC3966))?>"><?=htmlspecialchars($number->format(PhoneNumberFormat::NATIONAL))?></a>
        </p>
      </div>
      <?php } ?>
      <?php if ($info['ASANumber'] != null) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Swim England Number</h3>
        <p><a target="_blank" href="<?=htmlspecialchars('https://www.swimmingresults.org/membershipcheck/member_details.php?myiref=' . urlencode($info['ASANumber']))?>"><?=htmlspecialchars($info['ASANumber'])?> <i class="fa fa-external-link" aria-hidden="true"></i></a>
        </p>
      </div>

      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Swim England Category</h3>
        <p><?php if($info['ASACategory'] != 0) { ?><?=htmlspecialchars($info['ASACategory'])?><?php } else { ?><strong><span class="text-danger">Not set</span></strong><?php } ?>
        </p>
      </div>

      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Swim England Payment</h3>
        <p><?php if(bool($info['ASAPaid'])) { ?>Club pays <?=htmlspecialchars($info['Forename'])?>'s SE Membership<?php } else { ?><?=htmlspecialchars($info['Forename'])?> pays their own SE Membership<?php } ?>
        </p>
      </div>
      <?php } ?>

      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Club Membership</h3>
        <p><?php if (bool($info['ClubMember'])) { ?><?=htmlspecialchars($info['Forename'])?> is a club member<?php } else { ?><?=htmlspecialchars($info['Forename'])?> is not a club member<?php } ?></p>
      </div>

      <?php if (bool($info['ClubMember'])) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Club Membership Category</h3>
        <p><?php if($info['ClubCategory'] != null) { ?><?=htmlspecialchars($info['ClubCategory'])?><?php } else { ?><strong><span class="text-danger">Not set</span></strong><?php } ?>
        </p>
      </div>

      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Club Membership Payment</h3>
        <p><?php if(bool($info['ClubPaid'])) { ?>Club pays <?=htmlspecialchars($info['Forename'])?>'s Club Membership<?php } else { ?><?=htmlspecialchars($info['Forename'])?> pays their own Club Membership<?php } ?>
        </p>
      </div>
      <?php } ?>

      <?php if ($swimmerToo) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Sport</h3>
        <p><a href="<?=htmlspecialchars(autoUrl("members/" . $swimmerToo))?>"><?=htmlspecialchars($info['Forename'])?> is also a member</a></p>
      </div>
      <?php } ?>
    </div>
  </div>

  <?php if ($addr != null) { ?>
  <div class="mb-4">
    <h2>
      Residential address
    </h2>
    <address>
      <?=htmlspecialchars($addr->streetAndNumber)?><br>
      <?php if (isset($addr->flatOrBuilding)) { ?>
      <?=htmlspecialchars($addr->flatOrBuilding)?><br>
      <?php } ?>
      <?=htmlspecialchars(mb_strtoupper($addr->city))?><br>
      <?php if (isset($addr->county)) { ?>
      <?=htmlspecialchars($addr->county)?><br>
      <?php } ?>
      <?=htmlspecialchars(mb_strtoupper($addr->postCode))?>
    </address>
  </div>
  <?php } ?>

  <?php if ($info['AccessLevel'] == "Parent") { ?>
  <div class="mb-4">
    <h2>
      Monthly Fees
    </h2>
    <p class="lead">
      Monthly fees paid by this parent.
    </p>

    <div class="row">
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Squad Fees</h3>
        <p><?=monthlyFeeCost($db, $id, "string")?></p>
      </div>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Extra Fees</h3>
        <p><?=monthlyExtraCost($db, $id, "string")?></p>
      </div>
    </div>

    <?php if ($_SESSION['AccessLevel'] == 'Admin') { ?>
    <p>
      <a href="<?=htmlspecialchars(autoUrl("users/" . $id . "/membership-fees"))?>" class="btn btn-primary">
        Annual membership fees <span class="fa fa-chevron-right"></span>
      </a>
    </p>

    <p>
      <a href="<?=autoUrl("users/" . $id . "/pending-fees")?>" class="btn btn-primary">
        Pending payments <span class="fa fa-chevron-right"></span>
      </a>
    </p>

    <p>
      <a href="<?=autoUrl("payments/history/users/" . $id)?>" class="btn btn-primary">
        Previous bills <span class="fa fa-chevron-right"></span>
      </a>
    </p>
    <?php } ?>
  </div>

  <div class="mb-4">
    <h2>
      Members
    </h2>
    <p class="lead">
      Members linked to this account
    </p>

    <div class="row">
      <?php while ($s = $swimmers->fetch(PDO::FETCH_ASSOC)) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6"><a href="<?=autoUrl("swimmers/" . $s['id'])?>"
            title="Full information about <?=htmlspecialchars($s['fn'] . ' ' . $s['sn'])?>"><?=htmlspecialchars($s['fn'] . ' ' . $s['sn'])?></a>
        </h3>
        <?php if ($s['exempt'] || (int) $s['fee'] == 0) { ?>
        <p><?=htmlspecialchars($s['squad'])?> Squad, No squad fee</p>
        <?php } else { ?>
        <p><?=htmlspecialchars($s['squad'])?> Squad,
          &pound;<?=(string) (\Brick\Math\BigDecimal::of((string) $s['fee']))->toScale(2)?></p>
        <?php } ?>
      </div>
      <?php } ?>
    </div>
  </div>

  <?php if ($_SESSION['AccessLevel'] == 'Admin' && (env('GOCARDLESS_ACCESS_TOKEN') || env('GOCARDLESS_SANDBOX_ACCESS_TOKEN')) && !userHasMandates($id)) { ?>
  <div class="mb-4">
    <h2>
      Direct debit mandate settings
    </h2>
    <p class="lead">
      Authorise a direct debit opt out for this parent
    </p>

    <p>
      <a href="<?=autoUrl("users/" . $id . "/authorise-direct-debit-opt-out")?>" class="btn btn-primary">
        Authorise opt out <span class="fa fa-chevron-right"></span>
      </a>
    </p>
  </div>
  <?php } ?>

  <?php } ?>

  <?php if ($_SESSION['AccessLevel'] == 'Admin') { ?>
  <div class="row">
    <div class="col-12">
      <div class="mb-4">
        <h2>
          Squad rep settings
        </h2>
        <p class="lead">
          Make this user a squad rep.
        </p>

        <p>
          <a href="<?=autoUrl("users/" . $id . "/rep")?>" class="btn btn-primary">
            Rep settings <span class="fa fa-chevron-right"></span>
          </a>
        </p>
      </div>
    </div>

    <div class="col-12">
      <div class="mb-4">
        <h2>
          Targeted list settings
        </h2>
        <p class="lead">
          Grant a user permission to use a notify targeted list
        </p>

        <p>
          <a href="<?=autoUrl("users/" . $id . "/targeted-lists")?>" class="btn btn-primary">
            Notify lists <span class="fa fa-chevron-right"></span>
          </a>
        </p>
      </div>
    </div>

    <div class="col-12">
      <div class="mb-4">
        <h2>
          Team manager settings
        </h2>
        <p class="lead">
          Make this parent a team manager for a specific gala.
        </p>

        <p>
          <a href="<?=autoUrl("users/" . $id . "/team-manager")?>" class="btn btn-primary">
            Team manager settings <span class="fa fa-chevron-right"></span>
          </a>
        </p>
      </div>
    </div>
  </div>
  <?php } ?>

  <div class="mb-4">
    <h2>
      Access Control
    </h2>
    <p class="lead">
      Access Control oversees access to resources.
    </p>

    <div class="row">
      <div class="col-sm-6 col-md-4">
        <div class="input-group">
          <div class="input-group-prepend">
            <label class="input-group-text" for="accountType">Account Type</label>
          </div>
          <select class="custom-select" id="accountType" name="accountType" data-user-id="<?=htmlspecialchars($id)?>">
            <option <?=$par?> value="Parent">Parent (Default)</option>
            <option <?=$coa?> value="Coach">Coach</option>
            <option <?=$gal?> value="Galas">Galas</option>
            <option <?=$adm?> value="Admin">Admin</option>
          </select>
        </div>
        <div class="mt-2" id="accountTypeOutput"></div>
      </div>
    </div>
  </div>

  <div class="mb-4">
    <h2>
      Qualifications
    </h2>

    <p class="lead">
      The membership tracks qualifications to assist you in your compliance
      requirements.
    </p>

    <div class="row">
      <?php
    $count = 0;
    while ($qualification = $qualifications->fetch(PDO::FETCH_ASSOC)) {
      $count += 1; ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6"><?=htmlspecialchars($qualification['Name'])?></h3>
        <p><?=htmlspecialchars($qualification['Info'])?></p>
        <p>
          Valid since <?=date("d/m/Y", strtotime($qualification['From']))?><?php if ($qualification['To'] != null) { ?>,
          <strong>Expires <?=date("d/m/Y", strtotime($qualification['To']))?></strong><?php } ?>.
        </p>
      </div>
      <?php } ?>
      <?php if ($count == 0) { ?>
      <div class="col">
        <div class="alert alert-info">
          <strong>This user has no listed qualifications</strong><br>
          They may not have had any added
        </div>
      </div>
      <?php } ?>
    </div>

    <p>
      <a href="<?=currentUrl()?>qualifications" class="btn btn-primary">
        <span class="sr-only">View or add</span> Qualifications <span class="fa fa-chevron-right"></span>
      </a>
    </p>
  </div>

  <div class="mb-4">
    <h2>Simulate this user</h2>
    <p class="lead">
      Act as this user.
    </p>

    <p>
      You can use this feature to provide help and support to other users. It
      will be as if you have logged in as this user.
    </p>

    <p><a href="<?=autoUrl("users/simulate/" . $id)?>" class="btn btn-primary">Simulate this user <span
          class="fa fa-chevron-right"></span> </a></p>
  </div>

  <h2>
    Advanced Information
  </h2>
  <p class="lead">
    For troubleshooting.
  </p>

  <div class="mb-4">
    <div class="row">
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Last Login</h3>
        <?php
        $details = "";
        if ($loginInfo == null) {
          // User has never logged in
          $details = "This user has never logged in";
        } else {
          $time = new DateTime($loginInfo['Time'], new DateTimeZone('UTC'));
          $time->setTimezone(new DateTimeZone('Europe/London'));
          $details = $time->format('H:i T \o\n j F Y') . " from " . htmlspecialchars($loginInfo['Browser']) . " on " . htmlspecialchars($loginInfo['Platform']) . " (" . htmlspecialchars($loginInfo['IPAddress']) . ")";
        }?>
        <p><?=$details?></p>
      </div>
      <?php if (userHasMandates($id)) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Direct Debit Mandate</h3>
        <?php if ($logo_path) { ?>
        <img class="img-fluid mb-3" style="max-height:35px;" src="<?=$logo_path?>.png"
          srcset="<?=$logo_path?>@2x.png 2x, <?=$logo_path?>@3x.png 3x">
        <?php } ?>
        <p class="mb-0"><?=$bankName?><abbr
            title="<?=htmlspecialchars(mb_strtoupper(bankDetails($id, "bank_name")))?>"><?=htmlspecialchars(getBankName(bankDetails($id, "bank_name")))?></abbr>
        </p>
        <p class="mono">******<?=mb_strtoupper(bankDetails($id, "account_number_end"))?></p>
      </div>
      <?php } ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Account balance</h3>
        <p>
          &pound;<?=(string) (\Brick\Math\BigDecimal::of((string) getAccountBalance($id)))->withPointMovedLeft(2)->toScale(2)?>
        </p>
      </div>
    </div>
  </div>
</div>

<script src="<?=autoUrl("js/users/type-switch.js")?>"></script>

<?php include BASE_PATH . "views/footer.php";