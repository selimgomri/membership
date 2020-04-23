<?php

$db = app()->db;

try {
  $db->beginTransaction();

  $count = $db->prepare("SELECT COUNT(*) FROM notifyAdditionalEmails WHERE UserID = ? AND ID = ?");
  $count->execute([$_SESSION['UserID'], $id]);
  $before = $count->fetchColumn();

  $delete = $db->prepare("DELETE FROM notifyAdditionalEmails WHERE UserID = ? AND ID = ?");
  $delete->execute([$_SESSION['UserID'], $id]);

  $count = $db->prepare("SELECT COUNT(*) FROM notifyAdditionalEmails WHERE UserID = ? AND ID = ?");
  $count->execute([$_SESSION['UserID'], $id]);
  $after = $count->fetchColumn();

  $db->commit();

  if ($after < $before) {
    $_SESSION['DeleteCCSuccess'] = true;
  }
} catch (Exception $e) {
  $db->rollBack();
}

header("Location: " . autoUrl("my-account/email#cc"));
