<?php

$token = "testsecret";

$raw_payload = file_get_contents('php://input');

$headers = getallheaders();
$provided_signature = $headers["Webhook-Signature"];

$calculated_signature = hash_hmac("sha256", $raw_payload, $token);

if ($provided_signature == $calculated_signature) {
    $payload = json_decode($raw_payload, true);

    // Each webhook may contain multiple events to handle, batched together
    foreach ($payload["events"] as $event) {
        print("Processing event " . $event["id"] . "\n");

        switch ($event["resource_type"]) {
        case "mandates":
            process_mandate_event($event);
            break;
				case "payments":
            process_payment_event($event);
            break;
        default:
            print("Don't know how to process an event with resource_type " . $event["resource_type"] . "\n");
            break;
      }
    }

    header("HTTP/1.1 200 OK");
} else {
    header("HTTP/1.1 498 Invalid Token");
}

function process_mandate_event($event) {
	global $link;
	include BASE_PATH . 'controllers/payments/GoCardlessSetup.php';
  switch ($event["action"]) {
    case "created":
      if (mandateExists($event["links"]["mandate"])) {
        print("Mandate " . $event["links"]["mandate"] . " has been created!\n");
      } else {
        $mandateObject = $client->mandates()->get($event["links"]["mandate"]);
        $customer = $mandateObject->links->customer;
        $email = mysqli_real_escape_string($link, ($client->customers()->get($customer))->email);
        $sql = "SELECT `UserID` FROM `users` WHERE `EmailAddress` = '$email';";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) == 1) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          $user = $row['UserID'];

          $mandate = mysqli_real_escape_string($link, $event["links"]["mandate"]);
      		$customer = mysqli_real_escape_string($link, $customer);
      	  $bankAccount = mysqli_real_escape_string($link, $mandateObject->links->customer_bank_account);

      	  $bank = $client->customerBankAccounts()->get($bankAccount);
      	  $accHolderName = mysqli_real_escape_string($bank->account_holder_name);
      	  $accNumEnd = mysqli_real_escape_string($bank->account_number_ending);
      	  $bankName = mysqli_real_escape_string($bank->bank_name);

      		$sql1 = "INSERT INTO `paymentMandates` (`UserID`, `Name`, `Mandate`, `Customer`, `BankAccount`, `BankName`, `AccountHolderName`, `AccountNumEnd`, `InUse`) VALUES ('$user', 'Mandate', '$mandate', '$customer', '$bankAccount', '$bankName', '$accHolderName', '$accNumEnd', '1');";
      		mysqli_query($link, $sql1);

      		// If there is no preferred mandate existing, automatically set it to the one we've just added
      		$sql = "SELECT `MandateID` FROM `paymentMandates` WHERE `UserID` = '$user';";
      		$result = mysqli_query($link, $sql);
      		if (mysqli_num_rows($result) == 1) {
      			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      			$mandateId = $row['MandateID'];
      			$sql = "UPDATE `paymentPreferredMandate` SET `MandateID` = '$mandateId' WHERE `UserID` = '$user';";
      			mysqli_query($link, $sql);
      		}
          print("Mandate " . $event["links"]["mandate"] . " has been created! We found the user in question.\n");
        }
      }
      break;
  	case "cancelled":
      print("Mandate " . $event["links"]["mandate"] . " has been cancelled!\n");
			$mandate = mysqli_real_escape_string($link, $event["links"]["mandate"]);
			$sql = "UPDATE `paymentMandates` SET `InUse` = '0' WHERE `Mandate` = '$mandate';";
			mysqli_query($link, $sql);

			// Get the user ID, set to another bank if possible and let them know.
			$sql = "SELECT users.UserID, `Forename`, `Surname`, `EmailAddress` FROM `paymentMandates` INNER JOIN `users` ON users.UserID = paymentMandates.UserID WHERE `Mandate` = '$mandate';";
			$user = mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC);

			// If default unset
			$sql = "DELETE FROM `paymentPreferredMandate` WHERE `Mandate` = '$mandate';";
			mysqli_query($link, $sql);

			$userID = $user['UserID'];

			$sql = "UPDATE `paymentPreferredMandate` WHERE `UserID` = '$userID' LIMIT 1;";
			mysqli_query($link, $sql);

			$sql = "SELECT * FROM `paymentMandates` WHERE `UserID` = '$userID';";
			$rows = mysqli_num_rows(mysqli_query($link, $sql));

			if ($rows == 0) {
				$message = "<h1>Hello " . $user['Forename'] . " " . $user['Surname'] . ".</h1>
				<p>Your Direct Debit Mandate for Chester-le-Street ASC has been Cancelled. As this was your only mandate with us, you must set up a new direct debit as soon as possible at " . autoUrl("") . "</p>
				<p>Thank you, <br>Chester-le-Street ASC";
				notifySend($user['EmailAddress'], "Your Direct Debit Mandate has been Cancelled", $message);
			} else {
				$message = "<h1>Hello " . $user['Forename'] . " " . $user['Surname'] . ".</h1>
				<p>Your Direct Debit Mandate for Chester-le-Street ASC has been cancelled. As you had more than one direct debit set up, we've switched your default direct debit to the next available one in our list. You may want to check the details about this before we take any payments from you in order to ensure your're happy with us taking funds from that account.</p>
				<p>Go to " . autoUrl("") . " to make any changes.</p>
				<p>Thank you, <br>Chester-le-Street ASC";
				notifySend($user['EmailAddress'], "Your Direct Debit Mandate has been Cancelled", $message);
			}
      break;
		case "transferred":
			print("Mandate " . $event["links"]["mandate"] . " has been transferred to a new bank!\n");
			$mandate = mysqli_real_escape_string($link, $event["links"]["mandate"]);
			$bankAccount = mysqli_real_escape_string($link, ($client->mandates()->get($mandate))->links->customer_bank_account);

			$bank = $client->customerBankAccounts()->get($bankAccount);
		  $accHolderName = mysqli_real_escape_string($link, $bank->account_holder_name);
		  $accNumEnd = mysqli_real_escape_string($link, $bank->account_number_ending);
		  $bankName = mysqli_real_escape_string($link, $bank->bank_name);

			$sql = "UPDATE `paymentMandates` SET `BankAccount` = '$bankAccount', `AccountHolderName` = '$accHolderName', `AccountNumEnd` = '$accNumEnd', `BankName` = '$bankName' WHERE `Mandate` = '$mandate';";
			mysqli_query($link, $sql);
			break;
		case "expired":
			print("Mandate " . $event["links"]["mandate"] . " has expired!\n");
			$mandate = mysqli_real_escape_string($link, $event["links"]["mandate"]);
			$sql = "UPDATE `paymentMandates` SET `InUse` = '0' WHERE `Mandate` = '$mandate';";
			mysqli_query($link, $sql);

			// Get the user ID, set to another bank if possible and let them know.
			$sql = "SELECT users.UserID, `Forename`, `Surname`, `EmailAddress` FROM `paymentMandates` INNER JOIN `users` ON users.UserID = paymentMandates.UserID WHERE `Mandate` = '$mandate';";
			$user = mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC);

			// If default unset
			$sql = "DELETE FROM `paymentPreferredMandate` WHERE `Mandate` = '$mandate';";
			mysqli_query($link, $sql);

			$userID = $user['UserID'];

			$sql = "UPDATE `paymentPreferredMandate` WHERE `UserID` = '$userID' LIMIT 1;";
			mysqli_query($link, $sql);

			$sql = "SELECT * FROM `paymentMandates` WHERE `UserID` = '$userID';";
			$rows = mysqli_num_rows(mysqli_query($link, $sql));

			if ($rows == 0) {
				$message = "<h1>Hello " . $user['Forename'] . " " . $user['Surname'] . ".</h1>
				<p>Your Direct Debit Mandate for Chester-le-Street ASC has expired. As this was your only mandate with us, you must set up a new direct debit as soon as possible at " . autoUrl("") . "</p>
				<p>Thank you, <br>Chester-le-Street ASC";
				notifySend($user['EmailAddress'], "Your Direct Debit Mandate has Expired", $message);
			} else {
				$message = "<h1>Hello " . $user['Forename'] . " " . $user['Surname'] . ".</h1>
				<p>Your Direct Debit Mandate for Chester-le-Street ASC has expired. As you had more than one direct debit set up, we've switched your default direct debit to the next available one in our list. You may want to check the details about this before we take any payments from you in order to ensure your're happy with us taking funds from that account.</p>
				<p>Go to " . autoUrl("") . " to make any changes.</p>
				<p>Thank you, <br>Chester-le-Street ASC";
				notifySend($user['EmailAddress'], "Your Direct Debit Mandate has Expired", $message);
			}

			break;
    default:
      print("Don't know how to process a mandate " . $event["action"] . " event\n");
      break;
  }
}

function process_payment_event($event) {
	global $link;
	include BASE_PATH . 'controllers/payments/GoCardlessSetup.php';
  switch ($event["action"]) {
  	case "created":
      print("Payment " . $event["links"]["payment"] . " has been created!\n");
      if (!paymentExists($event["links"]["payment"])) {
        // Create a new Payment
        $PMkey = mysqli_real_escape_string($link, $event["links"]["payment"]);
        $payment = $client->payments()->get($PMkey);
        $status = mysqli_real_escape_string($link, $payment->status);
        $date = mysqli_real_escape_string($link, date("Y-m-d", strtotime($payment->created_at)));
        $amount = mysqli_real_escape_string($link, $payment->amount);
        $name = mysqli_real_escape_string($link, $payment->description);
        $currency = mysqli_real_escape_string($link, $payment->currency);
        $mandate = mysqli_real_escape_string($link, $payment->links->mandate);

        $sql = "SELECT `MandateID`, `UserID` FROM `paymentMandates` WHERE `Mandate` = '$mandate';";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) == 1) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          $user = mysqli_real_escape_string($link, $row['UserID']);
          $mandate = mysqli_real_escape_string($link, $row['MandateID']);
          $sql = "INSERT INTO `payments`
          (`Date`, `Status`, `UserID`, `MandateID`, `Name`, `Amount`, `Currency`, `PMkey`, `Type`)
          VALUES
          ('$date', '$status', '$user', '$mandate', '$name', '$amount', '$currency', '$PMkey', 'Payment');";
          mysqli_query($link, $sql);
        }
      }
      break;
		case "customer_approval_granted":
        if (updatePaymentStatus($event["links"]["payment"])) {
  			  print("Payment " . $event["links"]["payment"] . ": Customer Approval has been Granted\n");
        } else {
          print("Failed " . $event["links"]["payment"]);
        }
			break;
		case "customer_approval_denied":
			print("Mandate " . $event["links"]["mandate"] . " has expired!\n");
			break;
    case "submitted":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Submitted\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
		case "confirmed":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Confirmed\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
		case "chargeback_cancelled":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Chargeback Cancelled\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
    case "paid_out":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Paid Out\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
		case "late_failure_settled":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Late Failure Settled\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
		case "chargeback_settled":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Chargeback Settled\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
    case "failed":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Payment Failed\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
    case "charged_back":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Charged Back\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
		case "cancelled":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Cancelled\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
		case "resubmission_requested":
      if (updatePaymentStatus($event["links"]["payment"])) {
        print("Payment " . $event["links"]["payment"] . ": Resubmission Requested\n");
      } else {
        print("Event Failed " . $event["links"]["payment"]);
      }
      break;
    default:
      print("Don't know how to process a payment " . $event["action"] . " event\n");
      break;
  }
}
