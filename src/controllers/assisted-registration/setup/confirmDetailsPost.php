<?php

global $db;

use Respect\Validation\Validator as v;

$status = true;

$password1 = trim($_POST['password-1']);
$password2 = trim($_POST['password-2']);
$emailAuth = 0;
if ($_POST['emailAuthorise']) {
  $emailAuth = 1;
}
$smsAuth = 0;
if ($_POST['smsAuthorise']) {
  $smsAuth = 1;
}

$statusMessage = "<ul class=\"mb-0\">";

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

$statusMessage .= "<ul>";

if ($status) {
  $password = password_hash($password1, PASSWORD_BCRYPT);
  try {
    $update = $db->prepare("UPDATE users SET `Password` = ?, EmailComms = ?, MobileComms = ?, RR = ? WHERE UserID = ?");
    $update->execute([$password, $emailAuth, $smsAuth, $_SESSION['AssRegGuestUser'], 0]);
  } catch (Exception $e) {
    halt(500);
  }
  $_SESSION['AssRegStage'] = 3;
  header("Location: " . autoUrl("assisted-registration/go-to-onboarding"));
} else {
  $_SESSION['AssRegGetDetailsError'] = true;
  $_SESSION['AssRegGetDetailsPostData'] = $_POST;
  $_SESSION['AssRegGetDetailsMessage'] = $statusMessage;
  header("Location: " . currentUrl());
}