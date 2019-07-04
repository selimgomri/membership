<?php
$fluidContainer = true;


$require_email_auth = false;
$pagetitle = "My Account";
$userID = $_SESSION['UserID'];

$forenameUpdate = false;
$surnameUpdate = false;
$emailUpdate = false;
$mobileUpdate = false;
$emailCommsUpdate = false;
$mobileCommsUpdate = false;
$successInformation = "";
$emailChecked = "";
$mobileChecked = "";

global $db;

$getUser = $db->prepare("SELECT * FROM users WHERE UserID = ?");
$getUser->execute([$_SESSION['UserID']]);
$row = $getUser->fetch(PDO::FETCH_ASSOC);

$email = $row['EmailAddress'];
$forename = $row['Forename'];
$surname = $row['Surname'];
$access = $row['AccessLevel'];
$userID = $row['UserID'];
$mobile = $row['Mobile'];
$emailComms = $row['EmailComms'];
$mobileComms = $row['MobileComms'];

$update = false;

if (!empty($_POST['forename'])) {
if ($_POST['forename'] != $forename) {
  $update = $db->prepare("UPDATE `users` SET `Forename` = ? WHERE `UserID` = ?");
  $update->execute([trim(mb_convert_case($_POST['forename'], MB_CASE_TITLE_SIMPLE)), $_SESSION['UserID']]);
  $forenameUpdate = true;
  $update = true;
}
}
if (!empty($_POST['surname'])) {
if ($_POST['surname'] != $surname) {
  $update = $db->prepare("UPDATE `users` SET `Surname` = ? WHERE `UserID` = ?");
  $update->execute([trim(mb_convert_case($_POST['surname'], MB_CASE_TITLE_SIMPLE)), $_SESSION['UserID']]);
  $surnameUpdate = true;
  $update = true;
}
}

if (!empty($_POST['mobile'])) {
$newMobile = mysqli_real_escape_string($link, "+44" .
ltrim(preg_replace('/\D/', '', str_replace('+44', '', $_POST['mobile'])),
'0'));
if ($newMobile != $mobile) {
  $sql = "UPDATE `users` SET `Mobile` = '$newMobile' WHERE `UserID` = '$userID'";
  mysqli_query($link, $sql);
  $mobileUpdate = true;
  $update = true;
}
}
$post = app('request')->body;
if (app('request')->method == "POST") {
if (isset($post['emailContactOK']) && $post['emailContactOK'] == 1) {
  $sql = "UPDATE `users` SET `EmailComms` = '1' WHERE `UserID` = '$userID'";
  mysqli_query($link, $sql);
  if ($emailComms != 1) {
    $emailCommsUpdate = true;
    $update = true;
    $emailComms = 1;
  }
} else {
  $sql = "UPDATE `users` SET `EmailComms` = '0' WHERE `UserID` = '$userID'";
  mysqli_query($link, $sql);
  if ($emailComms == 1) {
    $emailCommsUpdate = true;
    $update = true;
    $emailComms = 0;
  }
}
if (isset($post['smsContactOK'])  && $post['smsContactOK'] == 1) {
  $sql = "UPDATE `users` SET `MobileComms` = '1' WHERE `UserID` = '$userID'";
  mysqli_query($link, $sql);
  if ($mobileComms != 1) {
    $mobileCommsUpdate = true;
    $mobileComms = 1;
    $update = true;
  }
} else {
  $sql = "UPDATE `users` SET `MobileComms` = '0' WHERE `UserID` = '$userID'";
  mysqli_query($link, $sql);
  if ($mobileComms == 1) {
    $mobileCommsUpdate = true;
    $update = true;
    $mobileComms = 0;
  }
}
}

if ($emailComms == 1) {
$emailChecked = " checked ";
}
if ($mobileComms == 1) {
$mobileChecked = " checked ";
}
//pre($_SESSION);

if ($update) {
$updateText = '<div class="alert alert-success mt-3">
  <strong>We have updated</strong>
  <ul class="mb-0">';
    if ($forenameUpdate) { $updateText .= '<li>Your first name</li>'; }
    if ($surnameUpdate) { $updateText .= '<li>Your last name</li>'; }
    if ($emailUpdate) { $updateText .= '<li>Your email address</li>'; }
    if ($mobileUpdate) { $updateText .= '<li>Your mobile number</li>'; }
    if ($emailCommsUpdate) { $updateText .= '<li>Your email preferences</li>'; }
    if ($mobileCommsUpdate) { $updateText .= '<li>Your mobile preferences</li>'; }
$updateText .= '
    </ul>
  </div>';
  $_SESSION['UserDetailsUpdate'] = $updateText;
}

if (app('request')->method == "POST") {
header("Location: " . currentUrl());
} else {

?>

<?php

include BASE_PATH . "views/header.php";

?>

<div class="container-fluid">
<div class="row justify-content-between">
<div class="col-md-3 d-none d-md-block">
  <?php
    $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/myaccount/ProfileEditorLinks.json'));
    echo $list->render('profile');
  ?>
</div>
<div class="col-md-9">
  <h1>Hello <?=htmlspecialchars($forename)?></h1>
  <p class="lead">Welcome to My Account where you can change your personal details, password, contact information and add swimmers to your account.</p>
  <?php if (isset($_SESSION['UserDetailsUpdate'])) {
    $userID = mysqli_real_escape_string($link, $_SESSION['UserID']);
    $query = "SELECT * FROM users WHERE UserID = '$userID';";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $email = $row['EmailAddress'];
    $forename = $row['Forename'];
    $surname = $row['Surname'];
    $access = $row['AccessLevel'];
    $userID = $row['UserID'];
    $mobile = $row['Mobile'];
    $emailComms = $row['EmailComms'];
    $mobileComms = $row['MobileComms'];
    if ($emailComms==1) {
      $emailChecked = " checked ";
    }
    if ($mobileComms==1) {
      $mobileChecked = " checked ";
    }
    echo $_SESSION['UserDetailsUpdate'];
    unset($_SESSION['UserDetailsUpdate']);
  } ?>
  <?php
  if ($require_email_auth) {
    echo '
    <div class="alert alert-warning mt-3 mb-0">
    To complete your change of email address, please check the link in your inbox.
    </div>';
  }
  ?>
  <div class="">
    <div class="">
      <div class="">
        <h2>Your Details</h2>
        <form method="post">
          <div class="form-row">
            <div class="col-md">
              <div class="form-group">
                <label for="forename">Name</label>
                <input type="text" class="form-control" name="forename" id="forename" placeholder="Forename" value="<?=htmlspecialchars($forename)?>">
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="surname">Surname</label>
                <input type="text" class="form-control" name="surname" id="surname" placeholder="Surname" value="<?=htmlspecialchars($surname)?>">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input readonly type="email" class="form-control" disabled name="email" id="emailbox" placeholder="Email Address" value="<?=htmlspecialchars($email)?>" aria-describedby="emailHelp">
            <p class="mb-0 mt-3">
              <a href="<?=autoUrl("my-account/email")?>" class="btn btn-secondary">
                Edit email address &amp; subscriptions
              </a>
            </p>
          </div>
          <div class="form-group">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" value="1" id="emailContactOK" aria-describedby="emailContactOKHelp" name="emailContactOK" <?=$emailChecked?> >
              <label class="custom-control-label" for="emailContactOK">Receive news by email</label>
              <small id="emailContactOKHelp" class="form-text text-muted">You'll still receive emails relating to your account if you don't receive news</small>
            </div>
          </div>
          <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <input type="tel" class="form-control" name="mobile" id="mobile" aria-describedby="mobileHelp" placeholder="Mobile Number" value="<?=htmlspecialchars($mobile)?>">
            <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number. Only <abbr title="United Kingdom (+44)">UK phone numbers</abbr> are accepted.</small>
          </div>
          <div class="form-group">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" value="1" id="smsContactOK" aria-describedby="smsContactOKHelp" name="smsContactOK" <?=$mobileChecked?> >
              <label class="custom-control-label" for="smsContactOK">Receive text messages</label>
              <small id="smsContactOKHelp" class="form-text text-muted">We'll still use this number to contact you in an emergency</small>
            </div>
          </div>
          <!--
          <div class="form-group" id="gravitar">
            <label for="mobile" class="d-block">Account Image</label>
            <?php
            $grav_url = "https://www.gravatar.com/avatar/" . md5( mb_strtolower( trim( $_SESSION['EmailAddress'] ) ) ) . "?d=" . urlencode("https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png") . "&s=240";
            ?>
            <img class="mr-3 rounded" src="<?=$grav_url?>" alt="" width="80" height="80">
            <small class="form-text text-muted">If you have <a href="https://en.gravatar.com/">an image linked to your email with Gravitar</a>, we'll display it in the system</small>
          </div>
          -->
          <p><input type="submit" class="btn btn-success" value="Save Changes"></p>
        </form>
        </div>

        <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
        <div class="cell">
          <h2>My Swimmers</h2>
          <p>Swimmers linked to your account</p>
          <?php echo mySwimmersTable($link, $userID) ?>
          <p><a href="<?php echo autoUrl("my-account/addswimmer"); ?>" class="btn btn-success">Add a Swimmer</a></p>
        </div>
      <?php } ?>
    </div>
    <div class="">
      <?php
      if ($_SESSION['AccessLevel'] == "Parent") {
        $contacts = new EmergencyContacts($db);
        $contacts->byParent($_SESSION['UserID']);

        $contactsArray = $contacts->getContacts();
        ?>
        <div class="cell">
          <h2>My Emergency Contacts</h2>
          <p class="border-bottom border-gray pb-2 mb-0">
            These are your emergency contacts
          </p>
          <?php if (sizeof($contactsArray) == 0) { ?>
            <div class="alert alert-warning mt-3">
              <p class="mb-0">
                <strong>
                  You have no emergency contacts
                </strong>
              </p>
              <p class="mb-0">
                Swim England rules require us to have details on file for you and at least one other person to contact in an emergency.
              </p>
            </div>
          <?php } else { ?>
          <div class="mb-3">
          <?php for ($i = 0; $i < sizeof($contactsArray); $i++) {
            ?>
            <div class="media pt-3">
              <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
                <div class="row align-items-center	">
                  <div class="col-9">
                    <p class="mb-0">
                      <strong class="d-block">
                        <?=htmlspecialchars($contactsArray[$i]->getName())?>
                      </strong>
                      <a href="tel:<?=htmlspecialchars($contactsArray[$i]->getContactNumber())?>">
                        <?=htmlspecialchars($contactsArray[$i]->getContactNumber())?>
                      </a>
                    </p>
                  </div>
                  <div class="col text-sm-right">
                    <a href="<?=autoUrl("emergency-contacts/edit/" .
                    $contactsArray[$i]->getID())?>" class="btn btn-primary">
                      Edit
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <?php
          } ?>
          </div>
          <?php } ?>
          <p class="mb-0">
            <a href="<?=autoUrl("emergency-contacts/new")?>" class="btn btn-success">
              Add New
            </a>
          </p>
        </div>
        <?php
      } ?>
    </div>
  </div>
</div>
</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>

<?php } ?>
