<?php

$db = app()->db;
$tenant = app()->tenant;

use Respect\Validation\Validator as v;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$countSwimmers = $db->prepare("SELECT COUNT(*) FROM members WHERE UserID = ?");
$countSwimmers->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser']]);
$rr = false;
if ($countSwimmers->fetchColumn() > 0) {
  $rr = true;
}

$getUser = $db->prepare("SELECT UserID, Forename, Surname, EmailAddress, Mobile, `Password` FROM users WHERE UserID = ?");
$getUser->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser']]);
$user = $getUser->fetch(PDO::FETCH_ASSOC);

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

// If need mobile
if (mb_strlen($user['Mobile']) == 0) {
  // Validate
  $mobile = null;
  try {
    $number = PhoneNumber::parse($_POST['mobile-number'], 'GB');
    $mobile = $number->format(PhoneNumberFormat::E164);
    $updateMobile = $db->prepare("UPDATE users SET Mobile = ? WHERE UserID = ?");
    $updateMobile->execute([
      $mobile,
      $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser']
    ]);
  } catch (PhoneNumberParseException $e) {
    // 'The string supplied is too short to be a phone number.'
    $status = false;
    $statusMessage .= "
  <li>Invalid mobile phone number</li>
  ";
  }
}

if (mb_strtoupper($tenant->getKey('ASA_CLUB_CODE')) == 'UOSZ') {
  // Check for user supplied ASA numbers
  $members = $db->prepare("SELECT MemberID, MForename, MSurname, ASANumber FROM members WHERE UserID = ? ORDER BY MForename ASC, MSurname ASC;");
  $members->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser'],
  ]);

  $updateAsa = $db->prepare("UPDATE members SET ASANumber = ? WHERE MemberID = ?");

  while ($member = $members->fetch(PDO::FETCH_ASSOC)) {
    if (isset($_POST['swim-england-' . $member['MemberID']]) && mb_strlen($_POST['swim-england-' . $member['MemberID']]) > 0) {
      try {
        $updateAsa->execute([
          mb_strimwidth($_POST['swim-england-' . $member['MemberID']], 0, 255),
          $member['MemberID']
        ]);
      } catch (PDOException $e) {
        // Ignore - not important for our purposes
      }
    }
  }
}

$statusMessage .= "<ul>";

if ($status) {
  $password = password_hash($password1, PASSWORD_ARGON2ID);
  try {
    $update = $db->prepare("UPDATE users SET `Password` = ?, EmailComms = ?, MobileComms = ?, RR = ? WHERE UserID = ?");
    $update->bindValue(1, $password, PDO::PARAM_STR);
    $update->bindValue(2, $emailAuth, PDO::PARAM_BOOL);
    $update->bindValue(3, $smsAuth, PDO::PARAM_BOOL);
    $update->bindValue(4, $rr, PDO::PARAM_BOOL);
    $update->bindValue(5, $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser'], PDO::PARAM_INT);
    $update->execute();

    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegStage'] = 3;
    header("Location: " . autoUrl("assisted-registration/go-to-onboarding"));
  } catch (Exception $e) {
    reportError($e);
    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError'] = true;
    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsPostData'] = $_POST;
    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsMessage'] = $statusMessage;
    header("Location: " . autoUrl("assisted-registration/confirm-details"));
  }
} else {
  $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError'] = true;
  $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsPostData'] = $_POST;
  $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsMessage'] = $statusMessage;
  header("Location: " . autoUrl("assisted-registration/confirm-details"));
}
