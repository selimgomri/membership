<?php

use Respect\Validation\Validator as v;
$db = app()->db;

$status = true;
$statusMessage = "";
$hash;

$updatePassword = $db->prepare("UPDATE `users` SET `Password` = :new WHERE `UserID` = :user");

try {
  $getPassword = $db->prepare("SELECT Password FROM users WHERE UserID = ?");
  $getPassword->execute([$_SESSION['UserID']]);
  $hash = $getPassword->fetchColumn();
} catch (Exception $e) {
  halt(500);
}

$currentPW = trim($_POST['current']);
$password1 = trim($_POST['new1']);
$password2 = trim($_POST['new2']);

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
  try {
    $newHash = password_hash($password1, PASSWORD_BCRYPT);
    $updatePassword->execute(['user' => $_SESSION['UserID'], 'new' => $newHash]);

    $_SESSION['PasswordUpdate'] = true;
    header("Location: " . autoUrl("my-account/password"));
  } catch (Exception $e) {
    halt(500);
  }
}
else {
  $_SESSION['ErrorState'] = '
  <div class="alert alert-danger">
  <p><strong>Something wasn\'t right</strong></p>
  <ul class="mb-0">' . $statusMessage . '</ul></div>';

  header("Location: " . autoUrl("my-account/password"));
}
?>
