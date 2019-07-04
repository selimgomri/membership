<?php

global $db;

try {
  $count = $db->prepare("SELECT COUNT(*) FROM linkedAccounts WHERE ID = ? AND (`User` = ? OR LinkedUser = ?)");
  $count->execute([$id, $_SESSION['UserID'], $_SESSION['UserID']]);

  if ($count->fetchColumn() > 0) {
    $delete = $db->prepare("DELETE FROM linkedAccounts WHERE ID = ? AND (`User` = ? OR LinkedUser = ?)");
    $delete->execute([$id, $_SESSION['UserID'], $_SESSION['UserID']]);
    $_SESSION['LinkedAccountDeleteSuccess'] = true;
  } else {
    $_SESSION['LinkedAccountDeleteError'] = true;
  }
} catch (Exception $e) {
  $_SESSION['LinkedAccountDeleteError'] = true;
}

header("Location: " . autoUrl("my-account/linked-accounts"));