<?php

$db = app()->db;

$getKey = $db->prepare("SELECT SquadKey FROM squads WHERE SquadID = ?");
$getKey->execute([$id]);
$squadDeleteKey = $getKey->fetchColumn();

if (mb_strlen(trim($_POST['squadName'])) == 0) {
  $_SESSION['UpdateError'] = true;
}
if ($_POST['squadFee'] < 0) {
  $_SESSION['UpdateError'] = true;
}

if ($_POST['squadDeleteDanger'] ==  $squadDeleteKey) {
  // Delete the squad
  try {
    $delete = $db->prepare("DELETE FROM squads WHERE SquadID = ?");
    $delete->execute([$id]);
  } catch (Exception $e) {
    $_SESSION['UpdateDatabaseError'] = true;
  }

  if ($_SESSION['UpdateDatabaseError']) {
    header("Location: " . autoUrl("squads/" . $id . "/edit"));
  } else {
    $_SESSION['DeleteSuccess'] = true;
    header("Location: " . autoUrl("squads"));
  }
} else {
  // Update the squad
  try {
    $update = $db->prepare("UPDATE squads SET SquadName = ?, SquadFee = ?, SquadCoach = ?, SquadTimetable = ?, SquadCoC = ? WHERE SquadID = ?");
    $update->execute([
      trim($_POST['squadName']),
      $_POST['squadFee'],
      trim($_POST['squadCoach']),
      trim($_POST['squadTimetable']),
      trim($_POST['squadCoC']),
      $id
    ]);
    $_SESSION['UpdateSuccess'] = true;
  } catch (Exception $e) {
    $_SESSION['UpdateDatabaseError'] = true;
  }
  header("Location: " . autoUrl("squads/" . $id . "/edit"));
}