<?php

\Stripe\Stripe::setApiKey(env('STRIPE'));
global $db;

if (!isset($_SESSION['GalaPaymentIntent'])) {
  halt(404);
}

handleCompletedGalaPayments($_SESSION['GalaPaymentIntent'], true);