<?php

global $db;

\Stripe\Stripe::setApiKey(env('STRIPE'));

if (!isset($_POST['method'])) {
  halt(404);
}

if (!isset($_SESSION['GalaPaymentIntent'])) {
  halt(404);
}

$getCards = $db->prepare("SELECT COUNT(*) `count`, MethodID, CustomerID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
$getCards->execute([$_SESSION['UserID'], $_POST['method']]);

$details = $getCards->fetch(PDO::FETCH_ASSOC);

if ($details['count'] > 0) {
  try {
    \Stripe\PaymentIntent::update(
      $_SESSION['GalaPaymentIntent'], [
        'payment_method' => $details['MethodID'],
      ]
    );
    $_SESSION['GalaPaymentMethodID'] = $_POST['method'];
    header("Location: " . autoUrl("galas/pay-for-entries/checkout"));
  } catch (Exception $e) {
    halt(500);
  }
} else {
  halt(404);
}