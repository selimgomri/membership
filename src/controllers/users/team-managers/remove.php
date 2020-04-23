<?php

$db = app()->db;

try {

  if (!isset($_GET['gala']) || $_GET['gala'] == null) {
    throw new Exception();
  }

  $insert = $db->prepare("DELETE FROM teamManagers WHERE `User` = ? AND `Gala` = ?");
  $insert->execute([
    $id,
    $_GET['gala']
  ]);

  // Success
  $_SESSION['RemoveGalaSuccess'] = true;
} catch (Exception $e) {
  $_SESSION['RemoveGalaError'] = true;
}

header("Location: " . autoUrl("users/" . $id . "/team-manager"));