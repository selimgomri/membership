<?php

\Stripe\Stripe::setApiKey(env('STRIPE'));

function stripe_handlePaymentMethodUpdate($pm) {
  global $db;

  $id = $pm->id;
  $city = $pm->billing_details->address->city;
  $country = $pm->billing_details->address->country;
  $line1 = $pm->billing_details->address->line1;
  $line2 = $pm->billing_details->address->line2;
  $postal_code = $pm->billing_details->address->postal_code;
  $expMonth = $pm->card->exp_month;
  $expYear = $pm->card->exp_year;
  $last4 = $pm->card->last4;
  $threeDSecure = $pm->card->three_d_secure_usage->supported;

  $update = $db->prepare("UPDATE stripePayMethods SET City = ?, Country = ?, Line1 = ?, Line2 = ?, PostCode = ?, ExpMonth = ?, ExpYear = ?, Last4 = ? WHERE MethodID = ?");
  $update->execute([
    $city,
    $country,
    $line1,
    $line2,
    $postal_code,
    $expMonth,
    $expYear,
    $last4,
    $id
  ]);
  echo "Success";
}

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  if (env('STRIPE_ENDPOINT_SECRET')) {
    $event = \Stripe\Webhook::constructEvent(
      $payload, $sig_header, $endpoint_secret
    );
  } else {
    $event = \Stripe\Event::constructFrom(
      json_decode($payload, true)
    );
  }
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Error\SignatureVerification $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}

// Handle the event
switch ($event->type) {
  case 'payment_method.card_automatically_updated':
    $paymentMethod = $event->data->object; // contains a \Stripe\PaymentIntent
    stripe_handlePaymentMethodUpdate($paymentMethod);
    break;
  case 'payment_method.updated':
    $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
    stripe_handlePaymentMethodUpdate($paymentMethod);
    break;
  default:
    // Unexpected event type
    http_response_code(400);
    exit();
}

http_response_code(200);
