<?php

$tenant = app()->tenant;

if ($tenant->getStripeAccount()) {
// USE_STRIPE_DIRECT_DEBIT
  if (isset($_POST['GALA_CARD_PAYMENTS_ALLOWED'])) {
    $tenant->setKey('GALA_CARD_PAYMENTS_ALLOWED', (int) bool($_POST['GALA_CARD_PAYMENTS_ALLOWED']));
  } else {
    $tenant->setKey('GALA_CARD_PAYMENTS_ALLOWED', (int) false);
  }

  if (!$tenant->getBooleanKey('USE_STRIPE_DIRECT_DEBIT')) {
    if (isset($_POST['USE_STRIPE_DIRECT_DEBIT'])) {
      $tenant->setKey('USE_STRIPE_DIRECT_DEBIT', (int) bool($_POST['USE_STRIPE_DIRECT_DEBIT']));
    }
  }

  // pre(bool($_POST['GALA_CARD_PAYMENTS_ALLOWED']));

}

header("location: " . autoUrl('settings/stripe'));