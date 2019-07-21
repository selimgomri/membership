<?php

global $db;

// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(env('STRIPE'));

$setupIntent = null;
if (!isset($_SESSION['StripeSetupIntentId'])) {
  halt(404);
} else {
  try {
    $setupIntent = \Stripe\SetupIntent::retrieve($_SESSION['StripeSetupIntentId']);
  } catch (Exception $e) {
    unset($_SESSION['StripeSetupIntentId']);
    header("Location: " . autoUrl("payments/cards/add"));
    return;
  }
}

$getUserEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$getUserEmail->execute([$_SESSION['UserID']]);
$user = $getUserEmail->fetch(PDO::FETCH_ASSOC);

$checkIfCustomer = $db->prepare("SELECT COUNT(*) FROM stripeCustomers WHERE User = ?");
$checkIfCustomer->execute([$_SESSION['UserID']]);

$customer = null;
if ($checkIfCustomer->fetchColumn() == 0) {
  // Create a Customer:
  $customer = \Stripe\Customer::create([
    "name" => $user['Forename'] . ' ' . $user['Surname'],
    "description" => "Customer for " . $_SESSION['UserID'] . ' (' . $user['EmailAddress'] . ')'
  ]);

  // YOUR CODE: Save the customer ID and other info in a database for later.
  $id = $customer->id;
  $addCustomer = $db->prepare("INSERT INTO stripeCustomers (User, CustomerID) VALUES (?, ?)");
  $addCustomer->execute([
    $_SESSION['UserID'],
    $id
  ]);
} else {
  $getCustID = $db->prepare("SELECT CustomerID FROM stripeCustomers WHERE User = ?");
  $getCustID->execute([$_SESSION['UserID']]);
  $customer = \Stripe\Customer::retrieve($getCustID->fetchColumn());
}

try {
  // Attach payment method to the customer:
  \Stripe\Customer::update(
    $customer->id,
    [
      'email' => $user['EmailAddress'],
      'phone' => $user['Mobile']
    ]
  );


  $pm = \Stripe\PaymentMethod::retrieve($setupIntent->payment_method);
  $pm->attach(['customer' => $customer->id]);

  $name = trim($_POST['name']);

  // Get the payment method details
  $id = $pm->id;
  $nameOnCard = $pm->card->name;
  $city = $pm->billing_details->address->city;
  $country = $pm->billing_details->address->country;
  $line1 = $pm->billing_details->address->line1;
  $line2 = $pm->billing_details->address->line2;
  $postal_code = $pm->billing_details->address->postal_code;
  $brand = $pm->card->brand;
  $issueCountry = $pm->card->country;
  $expMonth = $pm->card->exp_month;
  $expYear = $pm->card->exp_year;
  $funding = $pm->card->funding;
  $last4 = $pm->card->last4;
  $threeDSecure = $pm->card->three_d_secure_usage->supported;

  $getCardCount = $db->prepare("SELECT COUNT(*) FROM stripePayMethods WHERE Customer = ? AND Fingerprint = ? AND Reusable = ?");
  $getCardCount->execute([
    $customer->id,
    $pm->card->fingerprint,
    1
  ]);

  if ($getCardCount->fetchColumn() > 0) {
    $_SESSION['PayCardError'] = true;
    $_SESSION['PayCardErrorMessage'] = 'This card is already connected to your account';
    header("Location: " . autoUrl("payments/cards/add"));
    return;
  }

  $addPaymentDetails = $db->prepare("INSERT INTO stripePayMethods (Customer, MethodID, `Name`, CardName, City, Country, Line1, Line2, PostCode, Brand, IssueCountry, ExpMonth, ExpYear, Funding, Last4, Fingerprint, Reusable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $addPaymentDetails->execute([
    $customer->id,
    $id,
    $name,
    $nameOnCard,
    $city,
    $country,
    $line1,
    $line2,
    $postal_code,
    $brand,
    $issueCountry,
    $expMonth,
    $expYear,
    $funding,
    $last4,
    $pm->card->fingerprint,
    true,
  ]);

  unset($_SESSION['StripeSetupIntentId']);
  $_SESSION['PayCardSetupSuccess'] = true;
  $_SESSION['PayCardSetupSuccessBrand'] = getCardBrand($brand);
  header("Location: " . autoUrl("payments/cards"));
} catch (Exception $e) {
  $body = $e->getJsonBody();
  $err  = $body['error']['message'];
  $_SESSION['PayCardError'] = true;
  $_SESSION['PayCardErrorMessage'] = $err;
  header("Location: " . autoUrl("payments/cards/add"));
}