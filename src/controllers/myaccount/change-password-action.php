<?php

use Respect\Validation\Validator as v;

$status = true;
$statusMessage = "";

$username = $_SESSION['Username'];
$sql = "SELECT * FROM users WHERE Username = '$username' ";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);
$hash = $row['Password'];
$userID = $row['UserID'];

$currentPW = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['current'])));
$password1 = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['new1'])));
$password2 = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['new2'])));

if (!v::stringType()->length(7, null)->validate($password1)) {
  $status = false;
  $statusMessage .= "
  <li>Password does not meet the password length requirements. Passwords must be
  8 characters or longer</li>
  ";
}

if ($password1 != $password2) {
  $status = false;
  $statusMessage .= "
  <li>Passwords do not match</li>
  ";
}

if (!password_verify($currentPW, $hash)) {
  $status = false;
  $statusMessage .= "
  <li>Current password incorrect</li>
  ";
}

if ($status == true) {
  $newHash = password_hash($password1, PASSWORD_BCRYPT);
  $sql = "UPDATE `users` SET `Password` = '$newHash' WHERE `UserID` = '$userID'";
  mysqli_query($link, $sql);
  header("Location: " . autoUrl("myaccount"));
}
else {
  $_SESSION['ErrorState'] = '
  <div class="alert alert-danger">
  <p><strong>Something wasn\'t right</strong></p>
  <ul class="mb-0">' . $statusMessage . '</ul></div>';

  header("Location: " . autoUrl("myaccount/password"));
}
?>
