<?php

function stripe_handleBalanceTransactionForFees($balanceTransaction) {
  global $db;
  $update = $db->prepare("UPDATE stripePayments SET `Fees` = ? WHERE `Intent` = ?");

  try {
    $balanceTransaction = \Stripe\BalanceTransaction::retrieve([
      'id' => $balanceTransaction,
      'expand' => ['source'],
    ]);

    if (isset($balanceTransaction->source->payment_intent) && $balanceTransaction->source->payment_intent != null) {
      $update->execute([
        $balanceTransaction->fee,
        $balanceTransaction->source->payment_intent
      ]);
    }
  } catch (Exception $e) {
    reportError($e);
  }
}