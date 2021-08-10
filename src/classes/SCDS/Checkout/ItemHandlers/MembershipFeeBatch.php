<?php

namespace SCDS\Checkout\ItemHandlers;

class MembershipFeeBatch
{

  public static function paid($item, $stripePayment, $intentId)
  {

    $db = app()->db;
    $tenant = app()->tenant;
    \Stripe\Stripe::setApiKey(getenv('STRIPE'));

    // Get batch item
    $getBatchItem = $db->prepare("SELECT membershipBatchItems.ID, membershipBatchItems.Batch, membershipBatchItems.Membership, membershipBatchItems.Member, membershipBatchItems.Amount, membershipBatchItems.Notes, members.MForename, members.MSurname, clubMembershipClasses.Name FROM membershipBatchItems INNER JOIN members ON members.MemberID = membershipBatchItems.Member INNER JOIN clubMembershipClasses ON clubMembershipClasses.ID = membershipBatchItems.Membership WHERE membershipBatchItems.ID = ?");
    $getBatchItem->execute([
      $item->attributes->id,
    ]);
    $batchItem = $getBatchItem->fetch(\PDO::FETCH_OBJ);

    // Get info about the Stripe payment and method
    $intent = \Stripe\PaymentIntent::retrieve(
      [
        'id' => $intentId,
        'expand' => ['customer', 'payment_method', 'charges.data.balance_transaction'],
      ],
      [
        'stripe_account' => $tenant->getStripeAccount(),
      ]
    );

    // Update the batch to say it is paid
    $updateBatch = $db->prepare("UPDATE membershipBatch SET Completed = ?, PaymentDetails = ? WHERE ID = ?");
    $updateBatch->execute([
      (int) true,
      json_encode([
        'type' => 'stripe',
        'data' =>
        [
          'intent_id' => $stripePayment,
          'method_id' => $intent->payment_method->id,
          'payment_intent' => $intent,
        ]
      ]),
      $batchItem->Batch,
    ]);

    $addPaymentItems = $db->prepare("INSERT INTO stripePaymentItems (Payment, `Name`, `Description`, Amount, Currency, AmountRefunded) VALUES (?, ?, ?, ?, ?, ?)");
    $addPaymentItems->execute([
      $stripePayment,
      'Membership fees',
      $batchItem->MForename . ' ' . $batchItem->MSurname . ' - ' . $batchItem->Name,
      $item->amount,
      $item->currency,
      0,
    ]);
  }
}
