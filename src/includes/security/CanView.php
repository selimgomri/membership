<?php

function canView(String $section, int $user, int $event = null) {
  global $db;
  $al = $_SESSION['AccessLevel'];

  if ($section == 'TeamManager') {
    if ($al == 'Admin' || $al == 'Galas' || $al == 'Coach') {
      // All fine
      return true;
    } else {
      $getTeamManager = $db->prepare("SELECT COUNT(*) FROM teamManagers WHERE User = ? AND Gala = ?");
      $getTeamManager->execute([
        $user,
        $event
      ]);
      if ($getTeamManager->fetchColumn() == 0) {
        halt(404);
      }
    }
  }
}