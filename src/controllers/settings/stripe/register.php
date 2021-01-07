<?php

$tenant = app()->tenant;
$user = app()->user;

if ($at = app()->tenant->getStripeAccount()) {
  // Already go it, halt
  halt(404);
}

// You should store your client ID and secret in environment variables rather than
// committing them with your code
$client = new OAuth2\Client(getenv('STRIPE_CLIENT_ID'), getenv('STRIPE'));

$url = 'https://connect.stripe.com/oauth/authorize';

$_SESSION['Stripe-Reg-OAuth'] = [
  'tenant' => $tenant->getId(),
];

$authorizeUrl = $client->getAuthenticationUrl(
  $url,
  autoUrl("services/stripe/redirect", false), // Your redirect URL
  [
    'response_type' => 'code',
    'scope' => 'read_write',
  ]
);

// You'll now want to direct your user to the URL - you could redirect them or display it
// as a link on the page
header("Location: " . $authorizeUrl);
