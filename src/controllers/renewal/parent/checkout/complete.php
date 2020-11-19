<?php

\Stripe\Stripe::setApiKey(getenv('STRIPE'));
$db = app()->db;

if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'])) {
  halt(404);
}

$intent = $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'];
$reuse = 1;
/*
  if (isset($_POST['reuse-card']) && bool($reuse)) {
    $reuse = 1;
  }
  */
$intent = \Stripe\PaymentIntent::retrieve(
  [
    'id' => $paymentIntent,
    'expand' => ['customer', 'payment_method']
  ],
  [
    'stripe_account' => app()->tenant->getStripeAccount()
  ]
);

$getId = $db->prepare("SELECT ID FROM stripePayments WHERE Intent = ?");
$getId->execute([
  $intent->id
]);
$databaseId = $getId->fetchColumn();

if ($databaseId == null) {
  halt(404);
}

// If on session, go to success page
// Webhook handles fulfillment
if ($onSession && $intent->status == 'succeeded') {
  $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentDatabaseID'] = $databaseId;
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent']);
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentMethodID']);
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AddNewCard']);

  $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentSuccess'] = true;

  header("Location: " . autoUrl("renewal/payments/success"));
} else if ($onSession && $intent->status != 'succeeded') {
  header("Location: " . autoUrl("renewal/payments/checkout"));
}