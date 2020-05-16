<?php

use Respect\Validation\Validator as v;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$db = app()->db;
$tenant = app()->tenant;

try {

  $getUserInfo = $db->prepare("SELECT UserID FROM users WHERE EmailAddress = ?");

  $insert = $db->prepare("INSERT INTO users (EmailAddress, `Password`, Forename, Surname, Mobile, EmailComms, MobileComms, RR, Tenant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $addAccessLevel = $db->prepare("INSERT INTO `permissions` (`Permission`, `User`) VALUES (?, ?)");

  $forename = trim($_POST['first']);
  $surname = trim($_POST['last']);
  $email = $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUserEmail'];
  $getUserInfo->execute([$email]);
  
  // The password will be used as a secure token allowing the parent to follow a link.
  $password = hash('sha256', random_int(0, 999999));

  $status = true;

  $mobile = null;
  try {
    $number = PhoneNumber::parse($_POST['phone'], 'GB');
    $mobile = $number->format(PhoneNumberFormat::E164);
  }
  catch (PhoneNumberParseException $e) {
    // 'The string supplied is too short to be a phone number.'
    $status = false;
  }

  $info = $getUserInfo->fetchColumn();

  if ($info) {
    $update = $db->prepare("UPDATE users SET RR = ? WHERE UserID = ?");
    $update->execute([
      true,
      $info['UserID']
    ]);

    // Check has parent permissions
    $count = $db->prepare("SELECT COUNT(*) FROM `permissions` WHERE `User` = ? AND `Permission` = ?");
    $count->execute([
      $info['UserID'],
      'Parent'
    ]);
    if ($count->fetchColumn() == 0) {
      $addAccessLevel->execute([
        'Parent',
        $info['UserID']
      ]);
    }
    
    $_SESSION['AssRegUser'] = $info['UserID'];
    $_SESSION['AssRegExisting'] = true;
  } else if ($info == null) {
    // A random password is generated. This process involves the user setting a password later.
    $insert->execute([
      $email,
      password_hash($password, PASSWORD_BCRYPT),
      $forename,
      $surname,
      $mobile,
      0,
      0,
      0,
      $tenant->getId()
    ]);

    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUser'] = $db->lastInsertId();

    $addAccessLevel->execute([
      'Parent',
      $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUser']
    ]);
  } else {
    $status = false;
  }

  $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPass'] = $password;

} catch (Exception $e) {
  $status = false;
  reportError($e);
}

if ($status) {
  // Success move on
  header("Location: " . autoUrl("assisted-registration/select-swimmers"));
  if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUserEmail'])) {
    unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUserEmail']);
  }
} else {
  $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError'] = true;
  $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPostData'] = $_POST;
  header("Location: " . autoUrl("assisted-registration/start"));
}