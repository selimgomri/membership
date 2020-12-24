<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

$member = null;
try {
  $member = new Member($id);
} catch (Exception $e) {
  halt(404);
}

$user = $member->getUser();

$currentUser = app()->user;
if (!$currentUser->hasPermissions(['Admin'])) halt(404);

try {

  if (!\SCDS\CSRF::verify()) throw new Exception('Invalid CSRF token');

  // Check the qualification exists at this tenant
  $getQualifications = $db->prepare("SELECT `ID`, `Name`, `Description`, `DefaultExpiry` FROM `qualifications` WHERE `ID` = ? AND `Show` AND `Tenant` = ?");
  $getQualifications->execute([
    $_POST['qualification'],
    $tenant->getId(),
  ]);
  $qualification = $getQualifications->fetch(PDO::FETCH_ASSOC);

  if (!$qualification) {
    throw new Exception('No such qualification');
  }

  $validFrom = (new DateTime('now', new DateTimeZone('Europe/London')))->format('Y-m-d');
  $validTo = null;

  try {
    $validFrom = (new DateTime($_POST['valid-from'], new DateTimeZone('Europe/London')))->format('Y-m-d');
    if (isset($_POST['expires']) && $_POST['expires'] == 'yes') {
      $validTo = (new DateTime($_POST['valid-to'], new DateTimeZone('Europe/London')))->format('Y-m-d');
    }
  } catch (Exception $e) {
    // Ignore
  }

  $notes = null;
  if (isset($_POST['notes']) && mb_strlen(trim($_POST['notes'])) > 0) {
    $notes = trim($_POST['notes']);
  }

  $insert = $db->prepare("INSERT INTO `qualificationsMembers` (`ID`, `Qualification`, `Member`, `ValidFrom`, `ValidUntil`, `Notes`) VALUES (?, ?, ?, ?, ?, ?)");
  $insert->execute([
    Ramsey\Uuid\Uuid::uuid4()->toString(),
    $_POST['qualification'],
    $member->getId(),
    $validFrom,
    $validTo,
    $notes,
  ]);

  http_response_code(302);
  header('location: ' . autoUrl("members/$id#qualifications"));
} catch (Exception $e) {
  // Error

  http_response_code(302);
  header('location: ' . autoUrl("members/$id/qualifications/new"));
}
