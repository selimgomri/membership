<?php

global $db;

try {

  if (mb_strlen($_POST['name']) == 0) {
    throw new Exception();
  }

  $dateTimeObject = DateTime::createFromFormat ("Y-m-d H:i", $_POST['date'] . ' ' . $_POST['time'], new DateTimeZone('Europe/London'));
  $dateTimeObject->setTimezone(new DateTimeZone('UTC'));
  $time = $dateTimeObject->format("Y-m-d H:i:s");

  $meetUrl = "https://meet.myswimmingclub.uk/" . env('ASA_CLUB_CODE') . '-' . hash('sha256', $dateTimeObject->format("U") . random_int(PHP_INT_MIN, PHP_INT_MAX));

  $insert = $db->prepare("INSERT INTO meets (`Name`, StartTime, Creator, Link) VALUES (?, ?, ?, ?)");

  $insert->execute([
    trim($_POST['name']),
    $dateTimeObject->format("Y-m-d H:i:s"),
    $_SESSION['UserID'],
    $meetUrl
  ]);

  $_SESSION['NewMeetSuccess'] = true;
  header("location: " . autoUrl("meet"));

} catch (Exception $e) {

  $_SESSION['NewMeetError'] = true;
  header("location: " . autoUrl("meet/new"));

}