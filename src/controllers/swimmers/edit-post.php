<?php

$db = app()->db;
$tenant = app()->tenant;

$admin = app()->user->hasPermissions(['Admin', 'Coach']);

// Get all countries
$countries = getISOAlpha2CountriesWithHomeNations();

$updated = false;

$query = $db->prepare("SELECT * FROM members WHERE MemberID = ? AND Tenant = ?");
$query->execute([
  $id,
  $tenant->getId()
]);
$row = $query->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$isMemberUser = $row['UserID'] == app()->user->getId();

if (!$admin && $row['UserID'] != app()->user->getId()) {
  halt(404);
}

$getClubCategories = $db->prepare("SELECT `ID`, `Name` FROM `clubMembershipClasses` WHERE `Tenant` = ? ORDER BY `Name` ASC");
$getClubCategories->execute([
  $tenant->getId()
]);
$clubCategory = $getClubCategories->fetch(PDO::FETCH_ASSOC);

$adminMode = $admin && bool($_POST['is-admin']);
$memberUserMode = $isMemberUser && bool($_POST['is-member-user']);

// UPDATE DETAILS

$db->beginTransaction();

try {
  // GENERAL
  $update = $db->prepare("UPDATE `members` SET `MForename` = ?, `MSurname` = ?, `MMiddleNames` = ?, `DateOfBirth` = ?, `Gender` = ?, `OtherNotes` = ?");

  // Validate
  if (!isset($_POST['forename']) || mb_strlen(trim($_POST['forename'])) == 0) {
    throw new Exception('Forename not provided');
  }

  if (!isset($_POST['surname']) || mb_strlen(trim($_POST['surname'])) == 0) {
    throw new Exception('Surname not provided');
  }

  $middlenames = null;
  if (isset($_POST['middle-names']) || mb_strlen(trim($_POST['middle-names'])) > 0) {
    $middlenames = trim($_POST['middle-names']);
  }

  if (!isset($_POST['dob'])) {
    throw new Exception('Date of birth not provided');
  }

  $dob = null;
  $tomorrow = new DateTime('+1 day', new DateTimeZOne('Europe/London'));
  try {
    $dob = new DateTime($_POST['dob'], new DateTimeZone('Europe/London'));
  } catch (Exception $e) {
    throw new Exception('Invalid date of birth provided');
  }

  if ($dob > $tomorrow) {
    throw new Exception('Invalid date of birth provided (date in future)'); 
  }

  $acceptedSexes = ['Male', 'Female'];
  if (!isset($_POST['sex']) || !in_array($_POST['sex'], $acceptedSexes)) {
    throw new Exception('You did not provide a valid sex (for competition purposes)');
  }

  $otherNotes = '';
  if (isset($_POST['other-notes']) && mb_strlen(trim($_POST['other-notes'])) > 0) {
    $otherNotes = trim($_POST['other-notes']);
  }

  $update->execute([
    trim($_POST['forename']),
    trim($_POST['surname']),
    $middlenames,
    $dob->format('Y-m-d'),
    $_POST['sex'],
    $otherNotes,
  ]);

  // MEMBER USER STUFF
  if ($memberUserMode) {
    $update = $db->prepare("UPDATE `members` SET _______");
  }

  // ADMIN STUFF
  if ($adminMode) {
    $update = $db->prepare("UPDATE `members` SET `ASANumber` = ?, `ASACategory` = ?, `ClubCategory` = ?, `Country` = ?, `ASAPaid` = ?, `ClubPaid` = ?");

    $asaNumber = mb_strtoupper(app()->tenant->getKey('ASA_CLUB_CODE')) . $id;
    if (isset($_POST['asa']) && mb_strlen(trim($_POST['asa']))) {
      $asaNumber = mb_strtoupper(trim($_POST['asa']));
    }

    $asaCats = [0, 1, 2, 3];
    if (!isset($_POST['cat']) || !in_array((int) $_POST['cat'], $asaCats)) {
      throw new Exception('Invalid Swim England category');
    }

    if (!isset($_POST['club-cat'])) {
      throw new Exception('No club membership category supplied');
    }

    // Check club cat
    $checkCat = $db->prepare("SELECT COUNT(*) FROM `clubMembershipClasses` WHERE `ID` = ? AND `Tenant` = ?");
    $checkCat->execute([
      $_POST['club-cat'],
      $tenant->getId(),
    ]);

    if ($checkCat->fetchColumn() == 0) {
      throw new Exception('Invalid club membership category');
    }

    $country = 'GB-ENG';
    if (isset($_POST['country'])) {
      $country = $_POST['country'];
    }

    $asaPaid = false;
    if (isset($_POST['sep'])) {
      $asaPaid = bool($_POST['sep']);
    }

    $clubPaid = false;
    if (isset($_POST['cp'])) {
      $clubPaid = bool($_POST['cp']);
    }

    $update->execute([
      $asaNumber,
      (int) $_POST['cat'],
      $_POST['club-cat'],
      $country,
      (int) $asaPaid,
      (int) $clubPaid,
    ]);
  }

  AuditLog::new('Members-Edited', 'Edited ' . $row['MForename'] . ' ' . $row['MSurname'] . ' (#' . $id . ')');
  $db->commit();

  $_SESSION['TENANT-' . app()->tenant->getId()]['SuccessState'] = true;
} catch (Exception $e) {
  $db->rollBack();

  $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'] = $e->getMessage();
}

http_response_code(302);
header("location: " . autoUrl("members/$id/edit"));