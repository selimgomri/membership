<?php

$db = app()->db;
$tenant = app()->tenant;

$getYear = $db->prepare("SELECT `Name`, `StartDate`, `EndDate` FROM `membershipYear` WHERE `ID` = ? AND `Tenant` = ?");
$getYear->execute([
  $id,
  $tenant->getId(),
]);
$year = $getYear->fetch(PDO::FETCH_ASSOC);

if (!$year) halt(404);