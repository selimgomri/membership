<?php

$db = app()->db;
$tenant = app()->tenant;

$checkoutSession = \SCDS\Checkout\Session::retrieve($id);

if ($checkoutSession->user && $checkoutSession->user != app()->user->getId()) {
  halt(404);
}

// If not complete
if ($checkoutSession->state == 'succeeded' || $checkoutSession->getPaymentIntent()->status == 'succeeded') {
  // Else
  include 'success.php';
} else if ($checkoutSession->state == 'open') {
  include 'checkout.php';
}