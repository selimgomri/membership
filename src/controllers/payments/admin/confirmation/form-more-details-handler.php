<?php

/**
 * Handler for form-main
 * 
 * Tries to find a payment and show for confirmation to mark paid
 * 
 * or
 * 
 * Sends user to page for more details
 * More details include
 * Amount,
 * Payer
 */

global $db;

$_POST['payment-amount'];
$_POST['payment-name'];

$_SESSION['PaymentConfSearch']['payment-amount'] = $_POST['payment-amount'];
$_SESSION['PaymentConfSearch']['payment-name'] = $_POST['payment-name'];

// Search by reference
$findPayments = $db->prepare("SELECT COUNT(*) FROM payments WHERE PMkey LIKE ? COLLATE utf8mb4_general_ci AND `Type` = 'Payment'");
$findPayments->execute([
  '%' . $_POST['payment-ref'] . '%'
]);

if ($findPayments->fetchColumn() > 0) {
  // We may have it so we'll get matching IDs of all payments and show them 
  // to user to pick

  // Search by reference
  $findPayments = $db->prepare("SELECT PaymentID FROM payments WHERE PMkey LIKE ? COLLATE utf8mb4_general_ci AND `Type` = 'Payment'");
  $findPayments->execute([
    '%' . $_POST['payment-ref'] . '%'
  ]);

  $ids = [];
  while ($id = $findPayments->fetchColumn()) {
    $ids[] = $id;
  }
  $_SESSION['PaymentConfSearch']['id'] = $ids;
  header("Location: " . autoUrl("payments/confirmation/select-payment"));
} else {
  // Ask user for more detail
  header("Location: " . autoUrl("payments/confirmation/more-details"));
}