<?php

use Respect\Validation\Validator as v;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;
global $db;

try {

  $checkEmailCount = $db->prepare("SELECT COUNT(*) FROM users WHERE EmailAddress = ?");

  $insert = $db->prepare("INSERT INTO users (EmailAddress, `Password`, AccessLevel, Forename, Surname, Mobile, EmailComms, MobileComms, RR) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $forename = trim($_POST['first']);
  $surname = trim($_POST['last']);
  $email = trim(mb_strtolower($_POST['email-address']));
  $checkEmailCount->execute([$email]);
  
  // The password will be used as a secure token allowing the parent to follow a link.
  $password = hash('sha256', random_int(0, 999999));

  $status = true;
  if (!v::email()->validate($email)) {
    $status = false;
  }

  $mobile = null;
  try {
    $number = PhoneNumber::parse($_POST['phone'], 'GB');
    $mobile = $number->format(PhoneNumberFormat::E164);
  }
  catch (PhoneNumberParseException $e) {
    // 'The string supplied is too short to be a phone number.'
    $status = false;
    reportError($e);
  }

  if ($checkEmailCount->fetchColumn() > 0) {
    $update = $db->prepare("UPDATE users SET `Password` = ?, RR = ? WHERE EmailAddress = ?");
    $update->execute([
      password_hash($password, PASSWORD_BCRYPT),
      true,
      $email
    ]);

    $getUserId = $db->prepare("SELECT UserID FROM users WHERE EmailAddress = ?");
    $getUserId->execute([$email]);
    
    $_SESSION['AssRegUser'] = $getUserId->fetchColumn();
  } else {
    // A random password is generated. This process involves the user setting a password later.
    $insert->execute([
      $email,
      password_hash($password, PASSWORD_BCRYPT),
      'Parent',
      $forename,
      $surname,
      $mobile,
      0,
      0,
      true
    ]);

    $_SESSION['AssRegUser'] = $db->lastInsertId(); 
  }

  $_SESSION['AssRegPass'] = $password;

} catch (Exception $e) {
  $status = false;
  reportError($e);
}

if ($status) {
  // Success move on
  header("Location: " . autoUrl("assisted-registration/select-swimmers"));
} else {
  $_SESSION['AssRegFormError'] = true;
  $_SESSION['AssRegPostData'] = $_POST;
  header("Location: " . currentUrl());
}