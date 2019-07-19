<?php

global $db;

\Stripe\Stripe::setApiKey(env('STRIPE'));

if (!isset($_POST['method'])) {
  header("Location: " . autoUrl("galas/pay-for-entries/checkout"));
  return;
}

if (!isset($_SESSION['GalaPaymentIntent'])) {
  halt(404);
}

if ($_POST['method'] == 'new') {
  $_SESSION['AddNewCard'] = true;

  try {
    \Stripe\PaymentIntent::update(
      $_SESSION['GalaPaymentIntent'], [
        'payment_method' => null,
      ]
    );
    if (isset($_SESSION['GalaPaymentMethodID'])) {
      unset($_SESSION['GalaPaymentMethodID']);
    }
  } catch (Exception $e) {
    pre($e);
    halt(500);
  }
} else {
  if (isset($_SESSION['AddNewCard'])) {
    unset($_SESSION['AddNewCard']);
  }

  $getCards = $db->prepare("SELECT COUNT(*) `count`, MethodID, CustomerID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
  $getCards->execute([$_SESSION['UserID'], $_POST['method']]);

  $details = $getCards->fetch(PDO::FETCH_ASSOC);
  if ($details['count'] > 0) {
    try {
      \Stripe\PaymentIntent::update(
        $_SESSION['GalaPaymentIntent'], [
          'payment_method' => $details['MethodID'],
          'customer' => $details['CustomerID'],
        ]
      );
      $_SESSION['GalaPaymentMethodID'] = $_POST['method'];
    } catch (Exception $e) {
      pre($e);
      halt(500);
    }
  } else {
    halt(404);
  }
}

header("Location: " . autoUrl("galas/pay-for-entries/checkout"));