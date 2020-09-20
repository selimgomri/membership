<?php

use SCDS\Footer;

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

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

\Stripe\Stripe::setApiKey(getenv('STRIPE'));

try {

  $session = \Stripe\Checkout\Session::retrieve([
    'id' => $_GET['session_id'],
    'expand' => ['setup_intent'],
  ], [
    'stripe_account' => app()->tenant->getStripeAccount()
  ]);
  $intent = $session->setup_intent;

  if ($intent->status != 'succeeded') {
    throw new Exception('SetupIntent has not succeeded!');
  }

  // Setup - Adding to db is handled by webhook system

  $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess'] = true;
  header("location: " . autoUrl('registration-and-renewal/' . $id . '/direct-debit'));
} catch (Exception $e) {

  $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDError'] = true;
  header("location: " . autoUrl('registration-and-renewal/' . $id . '/direct-debit/set-up'));
}
