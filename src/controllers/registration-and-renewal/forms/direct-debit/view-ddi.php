<?php

use SCDS\Footer;

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

if (!isset($_GET['payment_method'])) {
  halt(404);
}

$getRenewal = $db->prepare("SELECT renewalData.ID, renewalPeriods.ID PID, renewalPeriods.Name, renewalPeriods.Year, renewalData.User, renewalData.Document FROM renewalData LEFT JOIN renewalPeriods ON renewalPeriods.ID = renewalData.Renewal LEFT JOIN users ON users.UserID = renewalData.User WHERE renewalData.ID = ? AND users.Tenant = ?");
$getRenewal->execute([
  $id,
  $tenant->getId(),
]);
$renewal = $getRenewal->fetch(PDO::FETCH_ASSOC);

if (!$renewal) {
  halt(404);
}

if (!$user->hasPermission('Admin') && $renewal['User'] != $user->getId()) {
  halt(404);
}

$ren = Renewal::getUserRenewal($id);

$renewalUser = new User($ren->getUser());

// Get mandates
$getMandates = $db->prepare("SELECT ID, Mandate, Last4, SortCode, `Address`, Reference, `URL`, `Status` FROM stripeMandates WHERE Customer = ? AND (`Status` = 'accepted' OR `Status` = 'pending') ORDER BY CreationTime DESC");
$getMandates->execute([
  $renewalUser->getStripeCustomer()->id,
]);
$mandate = $getMandates->fetch(PDO::FETCH_ASSOC);

$getMandates = $db->prepare("SELECT `URL` FROM stripeMandates WHERE Customer = ? AND ID = ?");
$getMandates->execute([
  $renewalUser->getStripeCustomer()->id,
  $_GET['payment_method'],
]);
$url = $getMandates->fetchColumn();

if (!$url) {
  halt(404);
} else {
  http_response_code(302);
  header("location: " . $url);
}
