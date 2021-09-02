<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

if (!isset($_POST['member']) || !isset($_POST['gala'])) halt(404);

$id = $_POST['member'];

$date = new DateTime('now', new DateTimeZone('Europe/London'));

$getMember = $db->prepare("SELECT MemberID, UserID, MForename, MSurname, DateOfBirth FROM members WHERE MemberID = ? AND Tenant = ?");
$getMember->execute([
  $id,
  $tenant->getId(),
]);

$member = $getMember->fetch(PDO::FETCH_ASSOC);

if (!$member) {
  halt(404);
}

$getGala = $db->prepare("SELECT GalaName FROM galas WHERE GalaID = ? AND GalaDate >= ? AND Tenant = ?");
$getGala->execute([
  $_POST['gala'],
  $date->format('Y-m-d'),
  $tenant->getId(),
]);

$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if (!$gala) {
  halt(404);
}

$getCountRep = $db->prepare("SELECT COUNT(*) FROM squadMembers WHERE Member = ? AND Squad IN (SELECT Squad FROM squadReps WHERE User = ?)");
$getCountRep->execute([
  $id,
  $user->getId(),
]);
$rep = $getCountRep->fetchColumn() > 0;

if (!$rep && !$user->hasPermission('Admin') && !$user->hasPermission('Coach') && !$user->hasPermission('Galas')) {
  if ($member['UserID'] != $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']) {
    halt(404);
  }
}

$getParent = $db->prepare("SELECT Forename, Surname FROM users WHERE UserID = ?");
$getParent->execute([
  $member['UserID'],
]);

$parent = $getParent->fetch(PDO::FETCH_ASSOC);

$today = new DateTime('now', new DateTimeZone('Europe/London'));
$age = new DateTime($member['DateOfBirth'], new DateTimeZone('Europe/London'));

$age = $age->diff($today);
$age = (int) $age->format('%y');

$addData = null;

http_response_code(302);

try {

  if (!isset($_POST['member-declaration'])) {
    throw new Exception('No member declaration given.');
  }

  if (app()->tenant->getKey('ASA_CLUB_CODE') == 'UOSZ' && !isset($_POST['uosswpc-member-declaration'])) {
    throw new Exception('No member declaration given to UoSSWPC agreement.');
  }

  $date = new DateTime('now', new DateTimeZone('UTC'));
  $today = new DateTime('now', new DateTimeZone('Europe/London'));
  $uuid = Ramsey\Uuid\Uuid::uuid4()->toString();

  $dob = new DateTime($member['DateOfBirth'], new DateTimeZone('Europe/London'));

  $age = $dob->diff($today);
  $age = (int) $age->format('%y');

  $memberAgreement = (int) bool($_POST['member-declaration']);

  $guardian = null;
  $guardianAgreement = null;
  if ($age < 18) {

    if (!isset($_POST['parent-declaration'])) {
      throw new Exception('No parent/guardian declaration given.');
    }

    $guardian = $member['UserID'];
    $guardianAgreement = (int) bool($_POST['parent-declaration']);

    if ($guardian == null) {
      throw new Exception('User requires approval by a parent/guardian, yet no guardian is on file.');
    }
  }

  // Add to database
  $add = $db->prepare("INSERT INTO `covidGalaHealthScreen` (`ID`, `DateTime`, `Member`, `MemberAgreement`, `Guardian`, `GuardianAgreement`, `Gala`) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $add->execute([
    $uuid,
    $date->format('Y-m-d H:i:s'),
    $id,
    $memberAgreement,
    $guardian,
    $guardianAgreement,
    $_POST['gala']
  ]);

  $_SESSION['CovidGalaSuccess'] = true;
  header('location: ' . autoUrl('covid/competition-health-screening'));
} catch (PDOException $e) {
  // throw new Exception('A database error occurred');
  $_SESSION['CovidGalaError'] = 'A database error occurred';
  header('location: ' . autoUrl('competition-health-screening/new-survey?member=' . $_POST['member'] . '?gala=' . $_POST['gala']));
} catch (Exception $e) {
  $_SESSION['CovidGalaError'] = $e->getMessage();
  header('location: ' . autoUrl('competition-health-screening/new-survey?member=' . $_POST['member'] . '?gala=' . $_POST['gala']));
}
