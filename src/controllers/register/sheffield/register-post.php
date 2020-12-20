<?php

pre($_POST);

$db = app()->db;
$tenant = app()->tenant;

use Respect\Validation\Validator as v;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$now = new DateTime('now', new DateTimeZone('Europe/London'));

// Check if user exists
$getUserCount = $db->prepare("SELECT COUNT(*) FROM users WHERE EmailAddress = ? AND Tenant = ?");

if (!isset($_POST['first']) || !isset($_POST['last']) || !isset($_POST['dob']) || !isset($_POST['email-address']) || !isset($_POST['mobile-number']) || !isset($_POST['password-1']) || !isset($_POST['password-2'])) {
  // Return to page
  http_response_code(302);
  header('location: ' . autoUrl('register/university-of-sheffield'));
} else {

  $db->beginTransaction();

  try {

    if (mb_strlen(trim($_POST['first'])) < 1 || mb_strlen(trim($_POST['last'])) < 1) {
      throw new Exception('No names provided');
    }

    $dob = null;
    try {
      $dob = new DateTime($_POST['dob'], new DateTimeZone('Europe/London'));
    } catch (Exception $e) {
      throw new Exception('The date of birth provided was invalid');
    }

    if ($dob > $now) {
      throw new Exception('Date of birth is in future.');
    }

    $emailAuth = 0;

    if (isset($_POST['emailAuthorise']) && $_POST['emailAuthorise'] == '1') {
      $emailAuth = true;
    }

    $password1 = trim($_POST['password-1']);
    $password2 = trim($_POST['password-2']);
    if (!v::stringType()->length(7, null)->validate($password1)) {
      throw new Exception('Password does not meet the password length requirements. Passwords must be 8 characters or longer.');
    }

    if ($password1 != $password2) {
      throw new Exception('Passwords do not match.');
    }

    // If need mobile
    $mobile = null;
    if (mb_strlen($user['Mobile']) == 0) {
      // Validate
      try {
        $number = PhoneNumber::parse($_POST['mobile-number'], 'GB');
        $mobile = $number->format(PhoneNumberFormat::E164);
      } catch (PhoneNumberParseException $e) {
        // 'The string supplied is too short to be a phone number.'
        $status = false;
        $statusMessage .= "
      <li>Invalid mobile phone number</li>
      ";
      }
    } else {
      throw new Exception('Mobile not provided');
    }

    $email = trim(mb_strtolower($_POST['email-address']));

    if (!v::email()->validate($email)) {
      throw new Exception('Invalid email address');
    }

    $getUserCount->execute([
      $email,
      $tenant->getId(),
    ]);

    if ($getUserCount->fetchColumn() > 0) {
      throw new Exception('A user with that email address already exists');
    }

    $firstName = mb_convert_case(trim($_POST['first']), MB_CASE_TITLE_SIMPLE);
    $lastName = mb_convert_case(trim($_POST['last']), MB_CASE_TITLE_SIMPLE);

    $swimEngland = '';

    $sex = 'Male';
    if (isset($_POST['sex']) && $_POST['sex'] == 'Female') {
      $sex = 'Female';
    }

    // Add user
    $addUser = $db->prepare("INSERT INTO users (EmailAddress, `Password`, Forename, Surname, Mobile, EmailComms, MobileComms, RR, Tenant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $addUser->execute([
      $email,
      password_hash($password1, PASSWORD_ARGON2ID),
      $firstName,
      $lastName,
      $mobile,
      $emailAuth,
      0,
      0,
      $tenant->getId(),
    ]);

    $userId = $db->lastInsertId();

    // Make parent
    $addPermission = $db->prepare("INSERT INTO `permissions` (`Permission`, `User`) VALUES (?, ?)");
    $addPermission->execute([
      'Parent',
      $userId,
    ]);

    // Add member
    $insertIntoSwimmers = $db->prepare("INSERT INTO members (UserID, MForename, MMiddleNames, Msurname, DateOfBirth, Gender, ASANumber, ASACategory, RR, AccessKey, OtherNotes, Tenant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertIntoSwimmers->execute([
      $userId,
      $firstName,
      '',
      $lastName,
      $dob->format('Y-m-d'),
      $sex,
      null,
      0,
      0,
      mb_substr(hash(random_bytes(64), 'sha256'), 0, 10),
      '',
      $tenant->getId(),
    ]);

    $memberId = $db->lastInsertId();

    $updateAsa = $db->prepare("UPDATE members SET ASANumber = ? WHERE MemberID = ?");
    if (isset($_POST['swim-england']) && mb_strlen(trim($_POST['swim-england'])) > 0) {
      $updateAsa->execute([
        mb_strimwidth(trim($_POST['swim-england']), 0, 255),
        $memberId,
      ]);
    } else {
      $updateAsa->execute([
        'TEMPORARY-' . mb_strtoupper($tenant->getKey('ASA_CLUB_CODE')) . '-' . $memberId,
        $memberId,
      ]);
    }

    $db->commit();

    // Login

    try {
      $login = new \CLSASC\Membership\Login($db);
      $login->setUser($userId);
      $login->stayLoggedIn();
      $login->preventWarningEmail();
      $currentUser = app()->user;
      $currentUser = $login->login();
    } catch (Exception $e) {
      halt(403);
    }

    // Go to homescreen

    http_response_code(302);
    header('location: ' . autoUrl(''));
  } catch (Exception $e) {
    $db->rollBack();

    $message = $e->getMessage();
    if (get_class($e) == 'PDOException') {
      $message = 'A database error occurred';
      reportError($e);
    }

    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError'] = true;
    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsPostData'] = $_POST;
    $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsMessage'] = $message;

    http_response_code(302);
    header('location: ' . autoUrl('register/university-of-sheffield'));
  }
}
