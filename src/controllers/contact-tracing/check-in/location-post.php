<?php

pre($_POST);

use function GuzzleHttp\json_decode;

$db = app()->db;
$tenant = app()->tenant;

$getLocation = $db->prepare("SELECT `ID`, `Name`, `Address` FROM covidLocations WHERE `ID` = ? AND `Tenant` = ?");
$getLocation->execute([
  $id,
  $tenant->getId()
]);
$location = $getLocation->fetch(PDO::FETCH_ASSOC);

if (!$location) {
  halt(404);
}

$guests = $members = null;
if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn']) && bool($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn'])) {
  $guests = $db->prepare("SELECT ID, GuestName, GuestPhone FROM covidVisitors WHERE Inputter = ?");
  $guests->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
  ]);
  $members = $db->prepare("SELECT MForename fn, MSurname sn, MemberID `id` FROM members WHERE `UserID` = ? ORDER BY fn ASC, sn ASC");
  $members->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
  ]);
}

try {



} catch (Exception $e) {
  $_SESSION['TENANT-' . app()->tenant->getId()]['ContactTracingError'] = [
    'post' => $_POST,
  ];
  header('location: ' . autoUrl('contact-tracing/check-in/' . $id));
}