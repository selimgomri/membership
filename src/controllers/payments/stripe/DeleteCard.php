<?php

$db = app()->db;

try {
  $getCard = $db->prepare("SELECT MethodID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
  $getCard->execute([
    $_SESSION['UserID'],
    $id
  ]);
  $card = $getCard->fetch(PDO::FETCH_ASSOC);

  if ($card == null) {
    halt(404);
  }

  $update = $db->prepare("UPDATE stripePayMethods SET Reusable = ? WHERE MethodID = ?");
  $update->execute([
    0,
    $card['MethodID']
  ]);

  try {
    \Stripe\Stripe::setApiKey(env('STRIPE'));

    $payment_method = \Stripe\PaymentMethod::retrieve($card['MethodID']);
    $payment_method->detach();
  } catch (Exception $e) {
    // Probably isn't the end of the world if this fails since system disables use of card.
  }

  $_SESSION['CardDeleted'] = true;
  header("Location: " . autoUrl("payments/cards"));
} catch (Exception $e) {
  halt(500);
}