<?php

$db = app()->db;

use Respect\Validation\Validator as v;

$countSwimmers = $db->prepare("SELECT COUNT(*) FROM members WHERE UserID = ?");
$countSwimmers->execute([$_SESSION['AssRegGuestUser']]);
$rr = false;
if ($countSwimmers->fetchColumn() > 0) {
  $rr = true;
}

$status = true;

$password1 = trim($_POST['password-1']);
$password2 = trim($_POST['password-2']);
$emailAuth = 0;
if (isset($_POST['emailAuthorise']) && $_POST['emailAuthorise'] == '1') {
  $emailAuth = true;
}
$smsAuth = 0;
if (isset($_POST['smsAuthorise']) && $_POST['smsAuthorise'] == '1') {
  $smsAuth = true;
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
    $update->bindValue(1, $password, PDO::PARAM_STR);
    $update->bindValue(2, $emailAuth, PDO::PARAM_BOOL);
    $update->bindValue(3, $smsAuth, PDO::PARAM_BOOL);
    $update->bindValue(4, $rr, PDO::PARAM_BOOL);
    $update->bindValue(5, $_SESSION['AssRegGuestUser'], PDO::PARAM_INT);
    $update->execute();
  } catch (Exception $e) {
    halt(500);
  }
  $_SESSION['AssRegStage'] = 3;
  header("Location: " . autoUrl("assisted-registration/go-to-onboarding"));
} else {
  $_SESSION['AssRegGetDetailsError'] = true;
  $_SESSION['AssRegGetDetailsPostData'] = $_POST;
  $_SESSION['AssRegGetDetailsMessage'] = $statusMessage;
  header("Location: " . autoUrl("assisted-registration/confirm-details"));
}