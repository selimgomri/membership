<?php

$swimsArray = [
  '50Free' => '50 Free',
  '100Free' => '100 Free',
  '200Free' => '200 Free',
  '400Free' => '400 Free',
  '800Free' => '800 Free',
  '1500Free' => '1500 Free',
  '50Back' => '50 Back',
  '100Back' => '100 Back',
  '200Back' => '200 Back',
  '50Breast' => '50 Breast',
  '100Breast' => '100 Breast',
  '200Breast' => '200 Breast',
  '50Fly' => '50 Fly',
  '100Fly' => '100 Fly',
  '200Fly' => '200 Fly',
  '100IM' => '100 IM',
  '150IM' => '150 IM',
  '200IM' => '200 IM',
  '400IM' => '400 IM'
];

\Stripe\Stripe::setApiKey(env('STRIPE'));

global $db;

if (!isset($_SESSION['GalaPaymentIntent'])) {
  halt(404);
}

$intent = \Stripe\PaymentIntent::retrieve($_SESSION['GalaPaymentIntent']);

if ($intent->status == 'succeeded') {
  $db->beginTransaction();

  $updateEntries = $db->prepare("UPDATE galaEntries SET Charged = ?, StripePayment = ? WHERE EntryID = ?");
  $addToStripePayments = $db->prepare("INSERT INTO stripePayments (`User`, `DateTime`, Method, Intent, Amount, Currency, ServedBy, Paid, AmountRefunded) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $addPaymentItems = $db->prepare("INSERT INTO stripePaymentItems (Payment, `Name`, `Description`, Amount, Currency, AmountRefunded) VALUES (?, ?, ?, ?, ?, ?)");
  $getEntry = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE EntryID = ? AND members.UserID = ?");

  $getPaymentMethod = $db->prepare("SELECT ID FROM stripePayMethods WHERE MethodID = ?");
  $getPaymentMethod->execute([$intent->payment_method]);
  $paymentMethodId = $getPaymentMethod->fetchColumn();
  if ($paymentMethodId == null) {
    halt(404);
  }

  $date = new DateTime('@' . $intent->created, new DateTimeZone('UTC'));

  try {
    $addToStripePayments->execute([
      $_SESSION['UserID'],
      $date->format("Y-m-d H:i:s"),
      $paymentMethodId,
      $intent->id,
      $intent->amount,
      $intent->currency,
      null,
      true,
      0
    ]);

    $databaseId = $db->lastInsertId();

    foreach ($_SESSION['PaidEntries'] as $entry => $details) {
      $updateEntries->execute([
        true,
        $databaseId, 
        $entry
      ]);

      $addPaymentItems->execute([
        $databaseId,
        'Gala entry',
        'Gala entry number ' . $entry,
        $details['Amount'],
        $intent->currency,
        0
      ]);
    }

    $_SESSION['CompletedEntries'] = $_SESSION['PaidEntries'];

    $message = "<p>Your payment receipt for gala entries.</p>";
    foreach ($_SESSION['CompletedEntries'] as $entry => $details) {
      $count = 0;
      $getEntry->execute([$entry, $_SESSION['UserID']]);
      $entry = $getEntry->fetch(PDO::FETCH_ASSOC);
      $message .= '<p>' . htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname']) . ' for ' . htmlspecialchars($entry['GalaName']) . '</p><ul>';
      foreach($swimsArray as $colTitle => $text) {
        if ($entry[$colTitle]) {
          $count++;
          $message .= '<li>' . $text . '</li>';
        }
      }
      $message .= '</ul>';
    } $message .= '<p>The total paid was £' . number_format($details['Amount']/100, 2) . '. The payment reference number is ' . $databaseId . '</p>';

    $email = $db->prepare("INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 1, 'Payments')");
    $email->execute([
      $_SESSION['UserID'],
      'Payment Receipt',
      $message
    ]);

    $db->commit();

    unset($_SESSION['GalaPaymentIntent']);
    unset($_SESSION['PaidEntries']);
    unset($_SESSION['GalaPaymentMethodID']);

    $_SESSION['GalaPaymentSuccess'] = true;

    header("Location: " . autoUrl("galas/pay-for-entries/success"));
  } catch (Exception $e) {
    pre($e);
    $db->rollBack();
  }
} else {
  header("Location: " . autoUrl("galas/pay-for-entries/checkout"));
}