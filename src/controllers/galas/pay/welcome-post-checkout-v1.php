<?php

\Stripe\Stripe::setApiKey(getenv('STRIPE'));
if (getenv('STRIPE_APPLE_PAY_DOMAIN')) {
  \Stripe\ApplePayDomain::create([
    'domain_name' => getenv('STRIPE_APPLE_PAY_DOMAIN')
  ]);
}

$db = app()->db;
$tenant = app()->tenant;

$updateTime = $db->prepare("UPDATE galaEntries SET FeeToPay = ? WHERE EntryID = ?");

$date = new DateTime('now', new DateTimeZone('Europe/London'));

$swimsArray = [
  '25Free' => '25 Free',
  '50Free' => '50 Free',
  '100Free' => '100 Free',
  '200Free' => '200 Free',
  '400Free' => '400 Free',
  '800Free' => '800 Free',
  '1500Free' => '1500 Free',
  '25Back' => '25 Back',
  '50Back' => '50 Back',
  '100Back' => '100 Back',
  '200Back' => '200 Back',
  '25Breast' => '25 Breast',
  '50Breast' => '50 Breast',
  '100Breast' => '100 Breast',
  '200Breast' => '200 Breast',
  '25Fly' => '25 Fly',
  '50Fly' => '50 Fly',
  '100Fly' => '100 Fly',
  '200Fly' => '200 Fly',
  '100IM' => '100 IM',
  '150IM' => '150 IM',
  '200IM' => '200 IM',
  '400IM' => '400 IM'
];

$rowArray = [1, null, null, null, null, null, 2, 1,  null, null, 2, 1, null, null, 2, 1, null, null, 2, 1, null, null, 2];
$rowArrayText = ["Freestyle", null, null, null, null, null, 2, "Backstroke",  null, null, 2, "Breaststroke", null, null, 2, "Butterfly", null, null, 2, "Individual Medley", null, null, 2];

try {
  $entries = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE members.UserID = ? AND (NOT RequiresApproval OR (RequiresApproval AND Approved)) AND NOT Charged AND FeeToPay > 0 AND galas.GalaDate >= ?");
  $entries->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], $date->format("Y-m-d")]);
} catch (Exception $e) {
  pre($e);
}
$entry = $entries->fetch(PDO::FETCH_ASSOC);

//pre($entry);

$payingEntries = [];

$checkoutSession = \SCDS\Checkout\Session::new([
  'user' => app()->user->getId(),
  'amount' => 1500,
]);

if ($entry != null) {
  do {
    $allGood = false;

    if (isset($_POST[$entry['EntryID'] . '-pay']) && bool($_POST[$entry['EntryID'] . '-pay'])) {
      $fee = 0;
      // Relace with if (true) once legacy period is over
      if (bool($entry['GalaFeeConstant'])) {
        $fee = \Brick\Math\BigDecimal::of((string) $entry['FeeToPay'])->withPointMovedRight(2)->toInt();
        $allGood = true;
      } else {
        if (isset($_POST[$entry['EntryID'] . '-pay']) && $_POST[$entry['EntryID'] . '-pay']) {
          if (isset($_POST[$entry['EntryID'] . '-amount']) && $_POST[$entry['EntryID'] . '-amount']) {
            $fee = $fee = \Brick\Math\BigDecimal::of((string) $_POST[$entry['EntryID'] . '-amount'])->withPointMovedRight(2)->toInt();
            $allGood = true;
            $updateTime->execute([
              number_format($_POST[$entry['EntryID'] . '-amount'], 2, '.', ''),
              $entry['EntryID']
            ]);
          } else {
            $fee = \Brick\Math\BigDecimal::of((string) $entry['FeeToPay'])->withPointMovedRight(2)->toInt();
            $allGood = true;
          }
        }
      }

      $itemEntries = [];
      foreach ($swimsArray as $key => $name) {
        if ($entry[$key]) {
          $itemEntries[] = [
            'name' => $name,
            'amount' => 0,
            'currency' => 'gbp',
            'attributes' => []
          ];
        }
      }

      $checkoutSession->addItem([
        'name' => $entry['GalaName'],
        'description' => $entry['MForename'] . ' ' . $entry['MSurname'],
        'amount' => $fee,
        'sub_items' => $itemEntries,
      ]);

      $payingEntries += [$entry['EntryID'] => [
        'Amount' => $fee,
        'UserEnteredAmount' => !$entry['GalaFeeConstant'],
        'Gala' => $entry['GalaID'],
        'Member' => $entry['MemberID']
      ]];
    }
  } while ($entry = $entries->fetch(PDO::FETCH_ASSOC));
}

if (false) {

  $_SESSION['TENANT-' . app()->tenant->getId()]['PaidEntries'] = $payingEntries;

  if (sizeof($payingEntries) > 0) {

    $total = 0;
    foreach ($payingEntries as $key => $value) {
      $total += $value['Amount'];
    }

    $updateEntryPayment = $db->prepare("UPDATE galaEntries SET StripePayment = ? WHERE EntryID = ?");

    $intent = \Stripe\PaymentIntent::create([
      'amount' => $total,
      'currency' => 'gbp',
      'payment_method_types' => ['card'],
      'confirm' => false,
      'setup_future_usage' => 'off_session',
      'metadata' => [
        'payment_category' => 'gala_entry',
        'user_id' => $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
      ]
    ], [
      'stripe_account' => $tenant->getStripeAccount()
    ]);
    $_SESSION['TENANT-' . app()->tenant->getId()]['GalaPaymentIntent'] = $intent->id;

    $intentCreatedAt = new DateTime('@' . $intent->created, new DateTimeZone('UTC'));

    // Check if intent already exists
    $checkIntentCount = $db->prepare("SELECT COUNT(*) FROM stripePayments WHERE Intent = ?");
    $checkIntentCount->execute([
      $intent->id
    ]);

    $databaseId = null;
    if ($checkIntentCount->fetchColumn() == 0) {
      // Add this payment intent to the database and assign the id to each entry
      $addIntent = $db->prepare("INSERT INTO stripePayments (`User`, `DateTime`, Method, Intent, Amount, Currency, Paid, AmountRefunded) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $addIntent->execute([
        $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
        $intentCreatedAt->format("Y-m-d H:i:s"),
        null,
        $intent->id,
        $intent->amount,
        $intent->currency,
        0,
        0
      ]);

      $databaseId = $db->lastInsertId();
    } else {
      $getIntentDbId = $db->prepare("SELECT ID FROM stripePayments WHERE Intent = ?");
      $getIntentDbId->execute([
        $intent->id
      ]);
      $databaseId = $getIntentDbId->fetchColumn();
    }
    $paymentDatabaseId = $databaseId;

    // Assign id to each entry
    foreach ($payingEntries as $entry => $details) {
      $updateEntryPayment->execute([
        $databaseId,
        $entry
      ]);
    }

    if ($total != $intent->amount) {
      $intent = \Stripe\PaymentIntent::update(
        $_SESSION['TENANT-' . app()->tenant->getId()]['GalaPaymentIntent'],
        [
          'amount' => $total,
        ],
        [
          'stripe_account' => $tenant->getStripeAccount()
        ]
      );
    }

    header("Location: " . autoUrl("galas/pay-for-entries/checkout"));
  } else {
    header("Location: " . autoUrl("galas/pay-for-entries"));
  }
}

header("Location: " . $checkoutSession->getUrl());