<?php

$db = app()->db;

try {

  if (!isset($_POST['squad-select']) || $_POST['squad-select'] == null) {
    throw new Exception();
  }

  $insert = $db->prepare("INSERT INTO squadReps (`User`, `Squad`) VALUES (?, ?)");
  $insert->execute([
    $id,
    $_POST['squad-select']
  ]);

  // Success
  $_SESSION['AssignSquadSuccess'] = true;
  header("Location: " . autoUrl("users/" . $id . "/rep"));
} catch (Exception $e) {
  // Success
  $_SESSION['AssignSquadError'] = true;
  header("Location: " . autoUrl("users/" . $id . "/rep/add"));
}