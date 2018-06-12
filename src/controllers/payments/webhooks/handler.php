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
  	case "cancelled":
      print("Mandate " . $event["links"]["mandate"] . " has been cancelled!\n");
      break;
		case "transferred":
			print("Mandate " . $event["links"]["mandate"] . " has been transferred to a new bank!\n");
			break;
		case "expired":
			print("Mandate " . $event["links"]["mandate"] . " has expired!\n");
			break;
    default:
      print("Don't know how to process a mandate " . $event["action"] . " event\n");
      break;
  }
}
