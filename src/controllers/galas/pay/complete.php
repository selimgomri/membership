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
  echo "1";
  halt(404);
}

$reuse = 1;
if (isset($_POST['reuse-card']) && bool($reuse)) {
  $reuse = 1;
}

$intent = \Stripe\PaymentIntent::retrieve($_SESSION['GalaPaymentIntent']);

if (isset($intent->charges->data[0]->payment_method_details->card->wallet)) {
  $reuse = 0;
}

$cardCount = 0;
$customerId = null;

$method = null;
$pm = null;

if (isset($newMethod) && $newMethod) {
  // Add payment intent

  $getUserEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
  $getUserEmail->execute([$_SESSION['UserID']]);
  $user = $getUserEmail->fetch(PDO::FETCH_ASSOC);

  $checkIfCustomer = $db->prepare("SELECT COUNT(*) FROM stripeCustomers WHERE User = ?");
  $checkIfCustomer->execute([$_SESSION['UserID']]);

  $customer = null;
  try {
    if ($checkIfCustomer->fetchColumn() == 0) {
      // Create a Customer:
      $customer = \Stripe\Customer::create([
        'payment_method' => $intent->payment_method,
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

    $method = $intent->payment_method;
    $pm = \Stripe\PaymentMethod::retrieve($method);

    $customerId = $customer->id;

    if (!isset($pm->customer) || $pm->customer != $customerId) {
      $pm->attach(['customer' => $customerId]);
    }
  
    $name = "Unnamed Card";
  
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

    $cardCount = $getCardCount->fetchColumn();

    if ($cardCount == 0) {  
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
        $reuse
      ]);
    }
  } catch (Exception $e) {
    //pre($e);
    $body = $e->getJsonBody();
    $err  = $body['error']['message'];
    $_SESSION['PayCardError'] = true;
    $_SESSION['PayCardErrorMessage'] = $err;
    header("Location: " . autoUrl("galas/pay-for-entries/checkout"));
    return;
  }
}

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
    if ($cardCount > 0) {
      $getCardFromOtherDetails = $db->prepare("SELECT ID FROM stripePayMethods WHERE Customer = ? AND Fingerprint = ? AND Reusable = ?");
      $getCardFromOtherDetails->execute([
        $customerId,
        $pm->card->fingerprint,
        1
      ]);
      $paymentMethodId = $getCardFromOtherDetails->fetchColumn();
      if ($paymentMethodId == null) {
        halt(404);
      }
    }
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

    if ($pm == null) {
      $pm = \Stripe\PaymentMethod::retrieve($intent->payment_method);
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
    }
    
    if ($pm != null && isset($pm->card)) {
      $message .= '<p>Paid with ' . getCardBrand($pm->card->brand) . ' ' . $pm->card->last4 . '.</p>';
    }
    $message .= '<p>The total paid was £' . number_format($details['Amount']/100, 2) . '. The payment reference number is ' . $databaseId . '.</p>';

    $emailDb = $db->prepare("INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, 1, 'Payments')");

    if (isset($intent->charges->data[0]->billing_details->email)) {
      $email = $intent->charges->data[0]->billing_details->email;
      $name = $_SESSION['Forename'] . ' ' . $_SESSION['Surname'];
      if (isset($intent->charges->data[0]->billing_details->name)) {
        $name = $intent->charges->data[0]->billing_details->name;
      }
      $message .= '<p>This receipt has been sent to the email address indicated when the payment was made, which may not be your club account email address.</p>';
      notifySend(null, 'Payment Receipt', $message, $name, $email, [
				"Email" => "payments@chesterlestreetasc.co.uk",
				"Name" => env('CLUB_NAME'),
				"Unsub" => [
					"Allowed" => true,
					"User" => $_SESSION['UserID'],
					"List" =>	"Payments"
				]
			]);
      $emailDb->execute([
        $_SESSION['UserID'],
        'Sent',
        'Payment Receipt',
        $message
      ]);
    } else {
      $emailDb->execute([
        $_SESSION['UserID'],
        'Queued',
        'Payment Receipt',
        $message
      ]);
    }

    $db->commit();

    unset($_SESSION['GalaPaymentIntent']);
    unset($_SESSION['PaidEntries']);
    unset($_SESSION['GalaPaymentMethodID']);
    unset($_SESSION['AddNewCard']);

    $_SESSION['GalaPaymentSuccess'] = true;

    header("Location: " . autoUrl("galas/pay-for-entries/success"));
  } catch (Exception $e) {
    pre($e);
    $db->rollBack();
  }
} else {
  header("Location: " . autoUrl("galas/pay-for-entries/checkout"));
}