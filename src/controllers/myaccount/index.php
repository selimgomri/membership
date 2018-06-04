<?php
  $pagetitle = "My Account";
  include BASE_PATH . "views/header.php";
  $username = $_SESSION['Username'];
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

  $query = "SELECT * FROM users WHERE UserID = '$userID' ";
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

  if (!empty($_POST['forename'])) {
    $newForename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
    if ($newForename != $forename) {
      $sql = "UPDATE `users` SET `Forename` = '$newForename' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $forenameUpdate = true;
    }
  }
  if (!empty($_POST['surname'])) {
    $newSurname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
    if ($newSurname != $surname) {
      $sql = "UPDATE `users` SET `Surname` = '$newSurname' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $surnameUpdate = true;
    }
  }
  if (!empty($_POST['email'])) {
    $newEmail = mysqli_real_escape_string($link, strtolower(trim(htmlspecialchars($_POST['email']))));
    if ($newEmail != $email) {
      // Check if email exists
      $sql = "SELECT `EmailAddress` FROM `users` WHERE EmailAddress = '$newEmail'";
      $test = mysqli_query($link, $sql);
      if (mysqli_num_rows($test) == 0) {
        $sql = "UPDATE `users` SET `EmailAddress` = '$newEmail' WHERE `UserID` = '$userID'";
        mysqli_query($link, $sql);
        $emailUpdate = true;
      }
    }
  }
  if (!empty($_POST['mobile'])) {
    $newMobile = mysqli_real_escape_string($link, preg_replace('/\D/', '', $_POST['mobile']));
    if ($newMobile != $mobile) {
      $sql = "UPDATE `users` SET `Mobile` = '$newMobile' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $mobileUpdate = true;
    }
  }
  if (isset($_POST['emailContactOK'])) {
    $newValue = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['emailContactOK'])));
    if ($newValue != 1) {$newValue = 0;}
    if ($newValue != $emailComms) {
      $sql = "UPDATE `users` SET `EmailComms` = '$newValue' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $emailCommsUpdate = true;
    }
  }
  if (isset($_POST['smsContactOK'])) {
    $newValue = mysqli_real_escape_string($link, preg_replace('/\D/', '', $_POST['smsContactOK']));
    if ($newValue != 1) {$newValue = 0;}
    if ($newValue != $mobileComms) {
      $sql = "UPDATE `users` SET `MobileComms` = '$newValue' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $mobileCommsUpdate = true;
    }
  }

?>
<div class="container">
<h1>Hello <?php echo $forename ?></h1>
<p class="lead">Welcome to My Account where you can change your personal details, password, contact information and add swimmers to your account.</p>
<?php if ($forenameUpdate || $surnameUpdate || $emailUpdate || $mobileUpdate) {
  $query = "SELECT * FROM users WHERE UserID = '$userID' ";
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
  $_SESSION['EmailAddress'] = $email;
  $_SESSION['Forename'] = $forename;
  $_SESSION['Surname'] = $surname;
  if ($emailComms==1) {
    $emailChecked = " checked ";
  }
  if ($mobileComms==1) {
    $mobileChecked = " checked ";
  }

?>
<div class="alert alert-success">
  <strong>We have updated</strong>
  <ul class="mb-0">
    <?php
    if ($forenameUpdate) { echo '<li>Your first name</li>'; }
    if ($surnameUpdate) { echo '<li>Your last name</li>'; }
    if ($emailUpdate) { echo '<li>Your email address</li>'; }
    if ($mobileUpdate) { echo '<li>Your mobile number</li>'; }
    if ($emailCommsUpdate) { echo '<li>Your email preferences</li>'; }
    if ($mobileCommsUpdate) { echo '<li>Your mobile preferences</li>'; }
    ?>
  </ul>
</div>
<?php  } ?>
<div class="my-3 p-3 bg-white rounded box-shadow">
  <h2>Your Details</h2>
  <p class="border-bottom border-gray pb-2">What we know about you.</p>
  <form method="post">
    <div class="form-group">
        <label for="forename">Name</label>
        <input type="text" class="form-control" name="forename" id="forename" placeholder="Forename" value="<?php echo $forename ?>">
     </div>
     <div class="form-group">
        <label for="surname">Surname</label>
        <input type="text" class="form-control" name="surname" id="surname" placeholder="Surname" value="<?php echo $surname ?>">
    </div>
     <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" value="<?php echo $email ?>">
    </div>
    <div class="form-group">
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" value="1" id="emailContactOK" aria-describedby="emailContactOKHelp" name="emailContactOK" <?php echo $emailChecked ?>>
        <label class="custom-control-label" for="emailContactOK">Check this to receive news by email</label>
        <small id="emailContactOKHelp" class="form-text text-muted">You'll still receive emails relating to your account if you don't receive news</small>
      </div>
    </div>
    <div class="form-group">
      <label for="mobile">Mobile Number</label>
      <input type="tel" class="form-control" name="mobile" id="mobile" aria-describedby="mobileHelp" placeholder="Mobile Number" value="<?php echo $mobile ?>">
      <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
    </div>
    <div class="form-group">
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" value="1" id="smsContactOK" aria-describedby="smsContactOKHelp" name="smsContactOK" <?php echo $mobileChecked ?>>
        <label class="custom-control-label" for="smsContactOK">Check this if you would like to receive text messages</label>
        <small id="smsContactOKHelp" class="form-text text-muted">We'll still use this to contact you in an emergency</small>
      </div>
    </div>
    <div class="form-group" id="gravitar">
      <label for="mobile" class="d-block">Account Image</label>
      <?php
      $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $_SESSION['EmailAddress'] ) ) ) . "?d=" . urlencode("https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png") . "&s=240";
      ?>
      <img class="mr-3" src="<?php echo $grav_url ?>" alt="" width="80" height="80">
      <small class="form-text text-muted">If you have an image $linked to your email with Gravitar, we'll display it in the system</small>
    </div>
    <p><input type="submit" class="btn btn-outline-dark" value="Save Changes"></p>
    <p class="mb-0">Changes will take place instantly. You can make changes again if you spot a mistake</p>
  </form>
  </div>

  <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h2>My Swimmers</h2>
    <p>Swimmers $linked to your account</p>
    <?php echo mySwimmersTable($link, $userID) ?>
    <p class="mb-0"><a href="add-swimmer.php" class="btn btn-outline-dark">Add a Swimmer</a></p>
  </div>
<?php } ?>
<div class="my-3 p-3 bg-white rounded box-shadow">
  <h2>Password</h2>
  <p class="border-bottom border-gray pb-2">Change your password regularly to keep your account safe</p>
  <p class="mb-0"><a href="change-password.php" class="btn btn-outline-dark">Change my Password</a></p>
</div>
<div class="my-3 p-3 bg-white rounded box-shadow">
  <h2>Technical Details</h2>
  <p class="border-bottom border-gray pb-2">Some things you can't change about your account</p>
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?php echo $username ?>" readonly>
    <small id="usernameHelp" class="form-text text-muted">You can't change your username.</small>
  </div>
  <div class="form-group">
    <label for="id">Unique User ID</label>
    <input type="number" class="form-control" name="id" id="id" placeholder="ID" value="<?php echo $userID ?>" readonly>
  </div>
  <div class="form-group">
    <label for="access">Access Level</label>
    <input type="text" class="form-control" name="access" id="access" placeholder="Access Level" value="<?php echo $access ?>" readonly>
  </div>
</div>
</div>

<?php include "/customers/9/d/e/chesterlestreetasc.co.uk/httpd.www/dev/membership/views/footer.php"; ?>
