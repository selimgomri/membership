<?php

$db = app()->db;
$tenant = app()->tenant;

$squadKey = generateRandomString(8);

if (isset($_POST['squadName']) && isset($_POST['squadFee'])) {
  try {
    $insert = $db->prepare("INSERT INTO squads (SquadName, SquadFee, SquadCoach, SquadCoC, SquadTimetable, SquadKey, Tenant) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([
      trim($_POST['squadName']),
      $_POST['squadFee'],
      trim($_POST['squadCoach']),
      $_POST['squadCoC'],
      trim($_POST['squadTimetable']),
      $squadKey,
      $tenant->getId()
    ]);
    header("Location: " . autoUrl('squads'));
  } catch (Exception $e) {
    halt(500);
  }
} else {
  header("Location: " . autoUrl('squads/addsquad'));
}

?>
