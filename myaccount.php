<?php
  $pagetitle = "My Account";
  include "header.php";
  $username = $_SESSION['Username'];
  $userID = $_SESSION['UserID'];

  $forenameUpdate = false;
  $surnameUpdate = false;
  $emailUpdate = false;
  $mobileUpdate = false;
  $successInformation = "";

  $query = "SELECT * FROM users WHERE UserID = '$userID' ";
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $email = $row['EmailAddress'];
  $forename = $row['Forename'];
  $surname = $row['Surname'];
  $access = $row['AccessLevel'];
  $userID = $row['UserID'];
  $mobile = $row['Mobile'];

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
    $newEmail = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['email'])));
    if ($newEmail != $email) {
      $sql = "UPDATE `users` SET `EmailAddress` = '$newEmail' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);
      $emailUpdate = true;
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

?>
<div class="container">
<h1>Your Account</h1>
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
  $_SESSION['EmailAddress'] = $email;
  $_SESSION['Forename'] = $forename;
  $_SESSION['Surname'] = $surname;
?>
<div class="alert alert-success">
  <strong>We have updated</strong>
  <ul class="mb-0">
    <?php
    if ($forenameUpdate) { echo '<li>Your first name</li>'; }
    if ($surnameUpdate) { echo '<li>Your last name</li>'; }
    if ($emailUpdate) { echo '<li>Your email address</li>'; }
    if ($mobileUpdate) { echo '<li>Your mobile number</li>'; }
    ?>
  </ul>
</div>
<?php  } ?>
<p>What we know about you.</p>
<form method="post" action="myaccount.php">
  <h2>Your Details</h2>
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
      <label for="mobile">Mobile Number</label>
      <input type="tel" class="form-control" name="mobile" id="mobile" placeholder="Mobile Number" value="<?php echo $mobile ?>">
      <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
  </div>
  <h2>My Swimmers</h2>
  <p><a href="add-swimmer.php" class="btn btn-light">Add a Swimmer</a></p>
  <h2>Password</h2>
  <p><a href="change-password.php" class="btn btn-light">Change my Password</a></p>
  <h2>Technical Details</h2>
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
  <p><input type="submit" class="btn btn-success" value="Save Changes"></p>
  <p>Changes will take place instantly. You can make changes again if you spot a mistake</p>
</form>
</div>

<?php include "footer.php"; ?>
