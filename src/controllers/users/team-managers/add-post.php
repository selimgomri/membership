<?php

$db = app()->db;

try {

  if (!isset($_POST['gala-select']) || $_POST['gala-select'] == null) {
    throw new Exception();
  }

  $insert = $db->prepare("INSERT INTO teamManagers (`User`, `Gala`) VALUES (?, ?)");
  $insert->execute([
    $id,
    $_POST['gala-select']
  ]);

  // Success
  $_SESSION['AssignGalaSuccess'] = true;
  header("Location: " . autoUrl("users/" . $id . "/team-manager"));
} catch (Exception $e) {
  // Success
  reportError($e);
  $_SESSION['AssignGalaError'] = true;
  header("Location: " . autoUrl("users/" . $id . "/team-manager/add"));
}