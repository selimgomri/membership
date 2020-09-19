<?php

$db = app()->db;
$tenant = app()->tenant;

$getCount = $db->prepare("SELECT COUNT(*) FROM `sessionsBookable` INNER JOIN `sessions` ON `sessions`.`SessionID` = `sessionsBookable`.`Session` WHERE `sessionsBookable`.`Session` = ? AND `sessions`.`Tenant` = ?");
$getCount->execute([
  $_GET['session'],
  $tenant->getId(),
]);

if ($getCount->fetchColumn() == 0) {
  include 'require-booking/require-booking.php';
} else {
  include 'book-session/book-session.php';
}