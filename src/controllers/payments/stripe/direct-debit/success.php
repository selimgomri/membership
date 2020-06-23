<?php

\Stripe\Stripe::setApiKey(env('STRIPE'));

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

  $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess'] = true;
  header("location: " . autoUrl("payments/direct-debit"));

} catch (Exception $e) {

  $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDError'] = true;
  header("location: " . autoUrl("payments/direct-debit/set-up"));

}