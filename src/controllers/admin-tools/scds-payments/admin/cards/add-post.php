<?php

$db = app()->db;
$tenant = app()->adminCurrentTenant;
$user = app()->adminCurrentUser;

\Stripe\Stripe::setApiKey(getenv('STRIPE'));

// UPDATING
$customer = $tenant->getStripeCustomer();

$setupIntent = null;
if (!isset($_SESSION['StripeSetupIntentId'])) {

  http_response_code(302);
  header('location: ' . autoUrl('payments-admin/payment-cards/add'));

} else {
  try {
    $setupIntent = \Stripe\SetupIntent::retrieve(
      $_SESSION['StripeSetupIntentId']
    );

    http_response_code(302);
    header("Location: " . autoUrl("payments-admin/payment-cards"));
  } catch (Exception $e) {
    unset($_SESSION['StripeSetupIntentId']);
    http_response_code(302);
    header("Location: " . autoUrl("payments-admin/payment-cards/add"));
    return;
  }
}