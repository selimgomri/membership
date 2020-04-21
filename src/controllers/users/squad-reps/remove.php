<?php

$db = app()->db;

try {

  if (!isset($_GET['squad']) || $_GET['squad'] == null) {
    throw new Exception();
  }

  $insert = $db->prepare("DELETE FROM squadReps WHERE `User` = ? AND `Squad` = ?");
  $insert->execute([
    $id,
    $_GET['squad']
  ]);

  // Success
  $_SESSION['RemoveSquadSuccess'] = true;
} catch (Exception $e) {
  $_SESSION['RemoveSquadError'] = true;
}

header("Location: " . autoUrl("users/" . $id . "/rep"));