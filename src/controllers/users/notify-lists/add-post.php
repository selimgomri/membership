<?php

$db = app()->db;

try {

  if (!isset($_POST['list-select']) || $_POST['list-select'] == null) {
    throw new Exception();
  }

  $insert = $db->prepare("INSERT INTO listSenders (`User`, `List`, `Manager`) VALUES (?, ?, ?)");
  $insert->execute([
    $id,
    $_POST['list-select'],
    0,
  ]);

  // Success
  $_SESSION['AssignListSuccess'] = true;
  header("Location: " . autoUrl("users/" . $id . "/targeted-lists"));
} catch (Exception $e) {
  // Success
  $_SESSION['AssignListError'] = true;
  header("Location: " . autoUrl("users/" . $id . "/targeted-lists/add"));
}