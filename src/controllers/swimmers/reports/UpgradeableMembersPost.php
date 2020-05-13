<?php

$db = app()->db;
$tenant = app()->tenant;

$allowedMembership = [
  0,
  1,
  2,
  3
];

try {

  $db->beginTransaction();

  $changeCat = $db->prepare("UPDATE members SET ASACategory = ? WHERE MemberID = ?");

  $date = new DateTime('-9 years last day of December', new DateTimeZone('Europe/London'));
  $now = new DateTime('now', new DateTimeZone('Europe/London'));

  $getMembers = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, SquadName squad, DateOfBirth dob, ASACategory cat FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE members.Tenant = ? AND DateOfBirth <= ? AND ASACategory = ? ORDER BY MForename ASC, MSurname ASC");
  $getMembers->execute([
  $tenant->getId(),
    $date->format("Y-m-d"),
    1
  ]);

  $changed = false;

  while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)) {
    if (isset($_POST["se-cat-" . $member['id']])) {
      if ($_POST["se-cat-" . $member['id']] != $member['cat'] && in_array($_POST["se-cat-" . $member['id']], $allowedMembership)) {
        // Update the membership category
        $changeCat->execute([
          $_POST["se-cat-" . $member['id']],
          $member['id']
        ]);
        $changed = true;
      }
    }
  }

  $db->commit();
  if ($changed) {
    $_SESSION['TENANT-' . app()->tenant->getId()]['CatChangesSavedSuccessfully'] = true;
  }
} catch (Exception $e) {
  $_SESSION['TENANT-' . app()->tenant->getId()]['CatChangesSaveError'] = true;
  $db->rollBack();
}

header("location: " . autoUrl("members/reports/upgradeable"));