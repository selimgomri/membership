<?php

$db = app()->db;
$tenant = app()->tenant;

$getLocation = $db->prepare("SELECT `ID`, `Name`, `Address` FROM covidLocations WHERE `ID` = ? AND `Tenant` = ?");
$getLocation->execute([
  $id,
  $tenant->getId()
]);
$location = $getLocation->fetch(PDO::FETCH_ASSOC);

if (!$location) {
  halt(404);
}

if (!app()->user) {
  halt(404);
}

$squad = null;

$db->beginTransaction();

try {
  if (!\SCDS\CSRF::verify()) {
    halt(401);
  }

  if (!isset($_POST['squad'])) {
    throw new Exception('Expected a value for \'squad\'');
  }

  // Get squad
  $user = app()->user;
  if ($user->hasPermission('Admin') || $user->hasPermission('Coach') || $user->hasPermission('Galas')) {
    $userSquads = $db->prepare("SELECT SquadName, SquadID FROM squads WHERE SquadID = ? AND Tenant = ? ORDER BY SquadFee DESC, SquadName ASC");
    $userSquads->execute([
      $_GET['squad'],
      $tenant->getId(),
    ]);
  } else {
    $userSquads = $db->prepare("SELECT SquadName, SquadID FROM squadReps INNER JOIN squads ON squadReps.Squad = squads.SquadID WHERE User = ? AND Squad = ? ORDER BY SquadFee DESC, SquadName ASC");
    $userSquads->execute([
      $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
      $_GET['squad'],
    ]);
  }

  $squad = $userSquads->fetch(PDO::FETCH_ASSOC);

  if (!$squad) {
    throw new Exception('No such squad');
  }

  // Get squad members
  $getMembers = $db->prepare("SELECT MemberID, MForename, MSurname, users.UserID, Forename, Surname, Mobile FROM members INNER JOIN squadMembers ON squadMembers.Member = members.MemberID INNER JOIN users ON members.UserID = users.UserID WHERE squadMembers.Squad = ? AND members.Tenant = ?;");
  $getMembers->execute([
    $_POST['squad'],
    $tenant->getId(),
  ]);

  $time = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
  $addRecord = $db->prepare("INSERT INTO covidVisitors (`ID`, `Location`, `Time`, `Person`, `Type`, `GuestName`, `GuestPhone`, `Inputter`, `Tenant`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");

  while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)) {

    // Check if present
    if (isset($_POST['member-' . $member['MemberID']]) && bool($_POST['member-' . $member['MemberID']])) {
      $addRecord->execute([
        (Ramsey\Uuid\Uuid::uuid4())->toString(),
        $id,
        $time,
        $member['MemberID'],
        'member',
        $member['MForename'] . ' ' . $member['MSurname'],
        $member['Mobile'],
        null,
        $tenant->getId(),
      ]);
    }
  }

  $db->commit();

  $_SESSION['TENANT-' . app()->tenant->getId()]['ContactTracingSuccess'] = true;

  http_response_code(302);
  header("location: " . autoUrl('contact-tracing/check-in/' . $id . '/success'));
} catch (PDOException $e) {
  // Generalise DB exceptions
  throw new Exception('A database error occurred');

  $db->rollBack();
} catch (Exception $e) {

  $_SESSION['TENANT-' . app()->tenant->getId()]['ContactTracingError'] = [
    'post' => $_POST,
    'message' => $e->getMessage(),
  ];

  http_response_code(302);
  if ($squad) {
    header("location: " . autoUrl('contact-tracing/check-in/' . $id . '?squad=' . $squad['SquadID']));
  } else {
    header("location: " . autoUrl('contact-tracing/check-in/' . $id));
  }
}
