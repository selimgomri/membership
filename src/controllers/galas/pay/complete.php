<?php

\Stripe\Stripe::setApiKey(env('STRIPE'));
$db = app()->db;

if (!isset($_SESSION['GalaPaymentIntent'])) {
  halt(404);
}

handleCompletedGalaPayments($_SESSION['GalaPaymentIntent'], true);