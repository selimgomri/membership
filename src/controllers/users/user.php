<?php

$use_white_background = true;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

global $db;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, AccessLevel FROM users WHERE UserID = ?");
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

$pagetitle = htmlspecialchars($info['Forename'] . ' ' . $info['Surname']) . " Information";
$title = null;
include BASE_PATH . "views/header.php";
?>
<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("users")?>">Users</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?></li>
    </ol>
  </nav>

  <h1>
    <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?>
    <small><?=htmlspecialchars($info['AccessLevel'])?></small>
  </h1>

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
        <p class="text-truncate"><a href="mailto:<?=htmlspecialchars($info['EmailAddress'])?>"><?=htmlspecialchars($info['EmailAddress'])?></a></p>
      </div>
      <?php if ($number !== false) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Phone</h3>
        <p><a href="<?=htmlspecialchars($number->format(PhoneNumberFormat::RFC3966))?>"><?=htmlspecialchars($number->format(PhoneNumberFormat::NATIONAL))?></a></p>
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
  </div>

  <div class="mb-4">
    <h2>
      Swimmers
    </h2>
    <p class="lead">
      This parent's swimmers.
    </p>

    <div class="row">
      <?php while ($s = $swimmers->fetch(PDO::FETCH_ASSOC)) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6"><a href="<?=autoUrl("swimmers/" . $s['id'])?>" title="Full information about <?=htmlspecialchars($s['fn'] . ' ' . $s['sn'])?>"><?=htmlspecialchars($s['fn'] . ' ' . $s['sn'])?></a></h3>
        <?php if ($s['exempt'] || (int) $s['fee'] == 0) { ?>
        <p><?=htmlspecialchars($s['squad'])?> Squad, No squad fee</p>
        <?php } else { ?>
        <p><?=htmlspecialchars($s['squad'])?> Squad, &pound;<?=number_format($s['fee'], 2)?></p>
        <?php } ?>
      </div>
      <?php } ?>
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
          <select class="custom-select" id="accountType" name="accountType">
            <option <?=$par?> value="Parent">Parent (Default)</option>
            <option <?=$coa?> value="Coach">Coach</option>
            <option <?=$com?> value="Committee">Team Manager</option>
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
        Valid since <?=date("d/m/Y", strtotime($qualification['From']))?><?php if ($qualification['To'] != null) { ?>, <strong>Expires <?=date("d/m/Y", strtotime($qualification['To']))?></strong><?php } ?>.
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
      <a href="<?=currentUrl()?>qualifications" class="btn rounded btn-success">
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

		<p><a href="<?=autoUrl("users/simulate/" . $id)?>" class="btn rounded btn-success">Simulate this user <span class="fa fa-chevron-right"></span> </a></p>
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
          $time = new DateTime($loginInfo['Time']);
          $details = $time->format('H:i \o\n j F Y') . " from " . htmlspecialchars($loginInfo['Browser']) . " on " . htmlspecialchars($loginInfo['Platform']) . " (" . htmlspecialchars($loginInfo['IPAddress']) . ")";
        }?>
        <p><?=$details?></p>
      </div>
      <?php if (userHasMandates($id)) { ?>
      <div class="col-sm-6 col-md-4">
        <h3 class="h6">Direct Debit Mandate</h3>
        <?php if ($logo_path) { ?>
        <img class="img-fluid mb-3" style="max-height:35px;" src="<?=$logo_path?>.png" srcset="<?=$logo_path?>@2x.png 2x, <?=$logo_path?>@3x.png 3x">
        <?php } ?>
        <p class="mb-0"><?=$bankName?><?=mb_strtoupper(bankDetails($id, "bank_name"))?></p>
        <p class="mono">******<?=mb_strtoupper(bankDetails($id, "account_number_end"))?></p>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

<script>
function apply() {
  var type = document.getElementById("accountType");
  var typeValue = type.value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("accountTypeOutput").innerHTML = this.responseText;
    }
  }
  xhttp.open("POST", "<?=autoUrl("users/ajax/userSettings/" . $id)?>", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("accountType=" + typeValue);
}

document.getElementById("accountType").onchange=apply;
</script>

<?php include BASE_PATH . "views/footer.php";
