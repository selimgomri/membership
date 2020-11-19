<?php

\Stripe\Stripe::setApiKey(getenv('STRIPE'));
$db = app()->db;

if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'])) {
  halt(404);
}

$renewal = $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalID'];
$paymentIntent = $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'];
$reuse = 1;
/*
  if (isset($_POST['reuse-card']) && bool($reuse)) {
    $reuse = 1;
  }
  */
$intent = \Stripe\PaymentIntent::retrieve(
  [
    'id' => $paymentIntent,
    'expand' => ['customer', 'payment_method']
  ],
  [
    'stripe_account' => app()->tenant->getStripeAccount()
  ]
);

$getId = $db->prepare("SELECT ID FROM stripePayments WHERE Intent = ?");
$getId->execute([
  $intent->id
]);
$databaseId = $getId->fetchColumn();

if ($databaseId == null) {
  halt(404);
}

// If on session, go to success page
// Webhook handles fulfillment
if ($intent->status == 'succeeded') {
  $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentDatabaseID'] = $databaseId;
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent']);
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentMethodID']);
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AddNewCard']);

  $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentSuccess'] = true;

  $progress = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = 0 WHERE `RenewalID` = ? AND `UserID` = ?");
  $progress->execute([
    $renewal,
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
  ]);

  if ($renewal != 0) {
    // Foreach check if in renewal members
    $countInRenewalMembers = $db->prepare("SELECT COUNT(*) FROM renewalMembers WHERE MemberID = ? AND RenewalID = ?");
    $insert = $db->prepare("INSERT INTO `renewalMembers` (`PaymentID`, `MemberID`, `RenewalID`, `Date`, `CountRenewal`, `Renewed`) VALUES (?, ?, ?, ?, ?, ?)");
    $update = $db->prepare("UPDATE renewalMembers SET PaymentID = ?, `Date` = ?, CountRenewal = ?, Renewed = ? WHERE MemberID = ? AND RenewalID = ?");

    for ($i = 0; $i < $count; $i++) {
      $countInRenewalMembers->execute([
        $member[$i]['MemberID'],
        $renewal
      ]);

      if ($countInRenewalMembers->fetchColumn() > 0) {
        // Update them
        $update->execute([
          null,
          $date->format("Y-m-d H:i:s"),
          true,
          true,
          $member[$i]['MemberID'],
          $renewal
        ]);
      } else {
        // Add them
        $insert->execute([
          null,
          $member[$i]['MemberID'],
          $renewal,
          $date->format("Y-m-d H:i:s"),
          true,
          true
        ]);
      }
    }
  }

  if (user_needs_registration($_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) {
    $sql = "UPDATE `users` SET `RR` = 0 WHERE `UserID` = ?";
    $query = $db->prepare($sql);
    $query->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

    $query = $db->prepare("UPDATE `members` SET `RR` = 0, `RRTransfer` = 0 WHERE `UserID` = ?");
    $query->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

    // Remove from status tracker
    $delete = $db->prepare("DELETE FROM renewalProgress WHERE UserID = ? AND RenewalID = ?");
    $delete->execute([
      $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
      $renewal
    ]);
    $location = autoUrl("");
  } else {
    $location = autoUrl("renewal/go");
  }

  unset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalID']);

  header("Location: " . autoUrl("renewal/go"));
} else if ($onSession && $intent->status != 'succeeded') {
  header("Location: " . autoUrl("renewal/payments/checkout"));
}
