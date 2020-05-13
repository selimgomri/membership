<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;
use Respect\Validation\Validator as v;

$db = app()->db;
$tenant = app()->tenant;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, ASANumber, ASAPrimary, ASACategory, ASAPaid, ClubMember, ClubPaid, ClubCategory FROM users WHERE UserID = ? AND Tenant = ?");
$userInfo->execute([
  $id,
  $tenant->getId()
]);

$info = $userInfo->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

try {

  $email = trim(mb_convert_case($_POST['email-address'], MB_CASE_LOWER));
  $mobile = trim($_POST['mobile-phone']);

  if (!v::email()->validate($email)) {
    $_SESSION['TENANT-' . app()->tenant->getId()]['InvalidEmail'] = true;
    throw new Exception();
  }

  $get = $db->prepare("SELECT COUNT(*) FROM users WHERE EmailAddress = ? AND UserID != ?");
  $get->execute([
    $email,
    $id
  ]);

  if ($get->fetchColumn() > 0) {
    $_SESSION['TENANT-' . app()->tenant->getId()]['UsedEmail'] = true;
    throw new Exception();
  }

  try {
    $mobile = PhoneNumber::parse($mobile, 'GB');
    $mobile = $mobile->format(PhoneNumberFormat::E164);
  } catch (PhoneNumberParseException $e) {
    $_SESSION['TENANT-' . app()->tenant->getId()]['InvalidPhone'] = true;
    throw new Exception();
  }

  $update = $db->prepare("UPDATE users SET Forename = ?, Surname = ?, EmailAddress = ?, Mobile = ? WHERE UserID = ?");
  $update->execute([
    trim(mb_convert_case($_POST['first-name'], MB_CASE_TITLE_SIMPLE)),
    trim(mb_convert_case($_POST['last-name'], MB_CASE_TITLE_SIMPLE)),
    $email,
    $mobile,
    $id,
  ]);

  $updateASA = $db->prepare("UPDATE users SET ASAMember = ?, ASANumber = ?, ASACategory = ?, ASAPrimary = ?, ASAPaid = ? WHERE UserID = ?");
  $updateMemberASA = $db->prepare("UPDATE members SET ASAMember = ?, ASACategory = ?, ASAPrimary = ?, ASAPaid = ? WHERE ASANumber = ?");
  if (bool($_POST['is-se-member'])) {
    // Update ASA things

    $asa = trim(mb_convert_case($_POST['se-number'], MB_CASE_UPPER));
    $asaCat = (int) $_POST['se-category'];
    $asaPrimary = (int) $_POST['is-se-primary-club'];
    $asaPaid = (int) $_POST['se-club-pays'];
    if (!bool($asaPrimary)) {
      $asaPaid = 0;
    }

    $updateASA->execute([
      1,
      $asa,
      $asaCat,
      $asaPrimary,
      $asaPaid,
      $id
    ]);

    $updateMemberASA->execute([
      1,
      $asaCat,
      $asaPrimary,
      $asaPaid,
      $asa
    ]);

  } else {
    // Make sure ASA things are unset
    $updateASA->execute([
      0,
      null,
      0,
      0,
      0,
      $id
    ]);
  }

  $updateMembership = $db->prepare("UPDATE users SET ClubMember = ?, ClubPaid = ?, ClubCategory = ? WHERE UserID = ?");
  if (bool($_POST['is-club-member'])) {
    // Update club things
    $clubMember = (int) $_POST['is-club-member'];
    $clubCat = (int) $_POST['club-category'];
    $clubPaid = (int) $_POST['club-club-pays'];

    $updateMembership->execute([
      $clubMember,
      $clubPaid,
      $clubCat,
      $id
    ]);
  } else {
    // Make sure club things are unset

    $updateMembership->execute([
      0,
      0,
      0,
      $id
    ]);
  }

  // Set access permissions
  $baseRequired = true;
  $userObject = new \User($id);

  // Galas
  if (bool($_POST['permissions-gala'])) {
    $userObject->grantPermission('Galas');
    $baseRequired = false;
  } else {
    $userObject->revokePermission('Galas');
  }

  // Coach
  if (bool($_POST['permissions-coach'])) {
    $userObject->grantPermission('Coach');
    $baseRequired = false;
  } else {
    $userObject->revokePermission('Coach');
  }

  // Admin
  if (bool($_POST['permissions-admin'])) {
    $userObject->grantPermission('Admin');
    $baseRequired = false;
  } else {
    $userObject->revokePermission('Admin');
  }

  // Parent
  if (bool($_POST['permissions-parent']) || $baseRequired) {
    $userObject->grantPermission('Parent');
  } else {
    $userObject->revokePermission('Parent');
  }

  $_SESSION['TENANT-' . app()->tenant->getId()]['Success'] = true;
} catch (Exception $e) {
  reportError($e);
  $_SESSION['TENANT-' . app()->tenant->getId()]['GeneralError'] = true;
}

header("Location: " . autoUrl("users/" . $id . "/edit"));