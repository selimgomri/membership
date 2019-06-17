<?php

use Respect\Validation\Validator as v;
global $db;

try {

  $insert = $db->prepare("INSERT INTO users (EmailAddress, `Password`, AccessLevel, Forename, Surname, Mobile, EmailComms, MobileComms, RR) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $forename = trim($_POST['first']);
  $surname = trim($_POST['last']);
  $email = trim($_POST['email-address']);
  $mobile = "+44" . ltrim(preg_replace('/\D/', '', str_replace("+44", "", trim($_POST['phone']))), '0'); // Removes anything that isn't a digit
  
  // The password will be used as a secure token allowing the parent to follow a link.
  $password = hash('sha256', random_int(0, 999999));

  $status = true;
  if (!v::email()->validate($email)) {
    $status = false;
  }

  if (!v::phone()->validate($mobile)) {
    $status = false;
  }

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
  $_SESSION['AssRegPass'] = $password;

} catch (Exception $e) {
  $status = false;
}

if ($status) {
  // Success move on
  header("Location: " . autoUrl("assisted-registration/select-swimmers"));
} else {
  $_SESSION['AssRegFormError'] = true;
  $_SESSION['AssRegPostData'] = $_POST;
  header("Location: " . currentUrl());
}