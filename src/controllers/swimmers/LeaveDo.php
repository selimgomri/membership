<?php

global $db;
$query = $db->prepare("SELECT UserID FROM members WHERE MemberID = ?");
$query->execute([$id]);
$result = $query->fetchColumn();

if ($result == null || $result != $_SESSION['UserID']) {
  halt(404);
}

$query = $db->prepare("SELECT COUNT(*) FROM moves WHERE MemberID = ?");
$query->execute([$id]);
$count = $query->fetchColumn();

if ($count != 0) {
  halt(500);
}

if ($_SESSION['LeaveKey'] == $key) {
  $query = $db->prepare("INSERT INTO moves (MemberID, SquadID, MovingDate) VALUES (?, ?, ?)");
  $query->execute([$id, 14, date("Y-m-01", strtotime('+1 month'))]);
  unset($_SESSION['LeaveKey']);
  $_SESSION['ConfirmLeave'] = true;
  header("Location: " . autoUrl("swimmers/" . $id . "/leaveclub/"));
} else {
  unset($_SESSION['LeaveKey']);
  halt(500);
}
