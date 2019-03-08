<?php

global $db;

$edit = $db->prepare("UPDATE sessionsVenues SET VenueName = ?, Location = ? WHERE VenueID = ?");

if ($_POST['name'] != "" && $_POST['name'] != null && $_POST['address'] != "" && $_POST['address'] != null) {
  try {
    $db->beginTransaction();
    $edit->execute([$_POST['name'], $_POST['address'], $id]);
    $db->commit();
    $_SESSION['EditVenueSuccess'] = true;
  } catch (Exception $e) {
    $db->rollback();
    halt(500);
  }
} else {
  $_SESSION['EditVenueError'] = [
    "Status"      => true,
    "Data"        => $_POST
  ];
}

header("Location: " . autoUrl("attendance/venues/" . $id));
