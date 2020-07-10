<?php

if (!isset($_SESSION['Stripe-Reg-OAuth'])) {
  halt(404);
}

$tenant = \Tenant::fromId((int) $_SESSION['Stripe-Reg-OAuth']['tenant']);

if (!$tenant || !isset($_GET['code'])) {
  halt(404);
}

\Stripe\Stripe::setApiKey(getenv('STRIPE'));

try {

  if (isset($_GET['error'])) {
    throw new Exception('User denied access');
  }

  if (!isset($_GET['scope']) || (isset($_GET['scope']) && $_GET['scope'] != "read_write")) {
    throw new Exception('User denied access');
  }

  $response = \Stripe\OAuth::token([
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
  ]);

  // Access the connected account id in the response
  $connected_account_id = $response->stripe_user_id;

  $tenant->setKey('STRIPE_ACCOUNT_ID', $connected_account_id);

  $_SESSION['TENANT-' . $tenant->getId()]['Stripe-Reg-Success'] = true;
  unset($_SESSION['Stripe-Reg-OAuth']['tenant']);

} catch (Exception $e) {
  $_SESSION['TENANT-' . $tenant->getId()]['Stripe-Reg-Error'] = true;
}

// Try to register Apple Pay domain
try {
  \Stripe\ApplePayDomain::create(
    ['domain_name' => app('request')->hostname],
    ['stripe_account' => $tenant->getStripeAccount()]
  );
} catch (Exception $e) {
  // Not the end of the world so report the error and continue.
  // Any errors can be resolved later.
  reportError($e);
}

header("location: " . autoUrl($tenant->getCodeId() . "/settings/stripe"));
