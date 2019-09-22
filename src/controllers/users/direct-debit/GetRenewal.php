<?php

global $db;
$latest = $db->query("SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() AND CURDATE() <= `EndDate` ORDER BY renewals.ID DESC LIMIT 1");
$latestRenewal = $latest->fetch(PDO::FETCH_ASSOC);

$getUserDetails = $db->prepare("SELECT RR FROM users WHERE UserID = ?");
$getUserDetails->execute([$person]);
$reg = $getUserDetails->fetchColumn();

$renewal = null;
if ($reg) {
  $renewal = 0;
} else if ($latestRenewal != null) {
  $renewal = $latestRenewal;
} else {
  throw new Exception('No renewal');
}