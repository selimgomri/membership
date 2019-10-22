<?php

function canView(String $section, int $user, int $event = null) {
  global $db;
  $al = $_SESSION['AccessLevel'];

  if ($section == 'TeamManager') {
    if ($al == 'Admin' || $al == 'Galas' || $al == 'Coach') {
      // All fine
      return true;
    } else {
      $date = new DateTime('-1 day', new DateTimeZone('Europe/London'));
      $getTeamManager = $db->prepare("SELECT COUNT(*) FROM teamManagers INNER JOIN galas ON galas.GalaID = teamManagers.Gala WHERE User = ? AND Gala = ? AND GalaDate >= ?");
      $getTeamManager->execute([
        $user,
        $event,
        $date->format("Y-m-d")
      ]);
      if ($getTeamManager->fetchColumn() == 0) {
        halt(404);
      }
    }
  }
}