<?php

ignore_user_abort(true);
set_time_limit(0);

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

global $db;
global $link;

$hasMandateQuery = null;
try {
  $sql = "SELECT * FROM `paymentMandates` INNER JOIN `paymentPreferredMandate` ON paymentMandates.UserID = paymentPreferredMandate.UserID WHERE paymentMandates.UserID = ? AND `InUse` = '1'";
  $hasMandateQuery = $db->prepare($sql);
} catch (Exception $e) {
  halt(500);
}

$date = date("Y-m") . "-01";
$day = date("d");

try {
  $getPayments = $db->prepare("SELECT payments.UserID, Amount, Currency, Name, PaymentID FROM payments LEFT JOIN paymentSchedule ON payments.UserID = paymentSchedule.UserID WHERE (Status = 'pending_api_request' AND Day <= ? AND Type = 'Payment') OR (Status = 'pending_api_request' AND `Day` IS NULL AND `Type` = 'Payment') LIMIT 4");
  $getPayments->execute([$day]);
  while ($row = $getPayments->fetch(PDO::FETCH_ASSOC)) {
  	$userid = $row['UserID'];
    try {
      $hasMandateQuery->execute([$userid]);
    } catch (Exception $e) {
      halt(500);
    }
    $mandateInfo = $hasMandateQuery->fetch(PDO::FETCH_ASSOC);

    $email_statment_id;

    $updatePayments = $db->prepare("UPDATE `payments` SET `Status` = ?, `MandateID` = ?, `PMkey` = ? WHERE `PaymentID` = ?");
    $updatePaymentsPending = $db->prepare("UPDATE `paymentsPending` SET `Status` = ?, `PMkey` = ? WHERE `UserID` = ? AND `Status` = ? AND `Type` = ? AND `Date` <= ?");

    if ($mandateInfo) {
    	$amount = $row['Amount'];
    	$currency = $row['Currency'];
    	$description = $row['Name'];
    	$idemKey = "PaymentID-" . $row['PaymentID'];
    	$mandate = $mandateInfo['Mandate'];
    	$mandateid = $mandateInfo['MandateID'];

      // MAKE AN API CALL TO GC
    	try {
    		$payment = $client->payments()->create([
    		  "params" => [
    	      "amount" => $amount, // 10 GBP in pence
    	      "currency" => $currency,
    				"description" => $description,
    	      "links" => [
    	        "mandate" => $mandate
    	      ],
    		  ],
    		  "headers" => [
    	      "Idempotency-Key" => $idemKey
    		  ]
    		]);

    		$paymentID = $row['PaymentID'];
    		$id = $payment->id;
        $email_statment_id = $id;
    		$status = $payment->status;

        $updatePayments->execute([
          $status,
          $mandateid,
          $id,
          $paymentID
        ]);

        $updatePaymentsPending->execute([
          'Requested',
          $id,
          $userid,
          'Queued',
          'Payment',
          $date
        ]);

    	} catch (Exception $e) {
      	halt(500);
    	}
    } else {
      try {
        $paymentID = $row['PaymentID'];
        $id = "CASH" . $paymentID;
        $email_statment_id = $id;

        $updatePayments->execute([
          'cust_not_dd',
          null,
          $id,
          $paymentID
        ]);

        $updatePaymentsPending->execute([
          'Paid',
          $id,
          $userid,
          'Queued',
          'Payment',
          $date
        ]);
      } catch (Exception $e) {
        halt(500);
      }
    }

    $message_subject = "Your bill for " . date("F Y") . " is now available";
    $message_content = '';
    if (!$mandateInfo) {
      $message_content .= '<div class="cell"><p><strong>Warning: You do not have a direct debit set up with us.</strong>';
      if (bool(env('IS_CLS'))) {
        $message_content .= ' If you are paying us manually, a £3 surcharge applies to your monthly fee. Login to <a href="' . autoUrl("payments") . '">the Membership System</a> to set up a direct debit before next month\'s bill.</p>';
        $message_content .= '<p class="mb-0">If you expected to pay us by direct debit this month, please contact the treasurer as soon as possible - Your swimmers could be suspended if you fail to pay squad fees.';
      }
      $message_content .= '</p></div>';
    }
    $message_content .= '<p>Your bill for ' . date("F Y") . ' is now available. Here are your squad fees for this month.</p>';
    $message_content .= myMonthlyFeeTable($link, $row['UserID']);
    $message_content .= '<p>Combined with any additional fees such as membership, your total fee for ' . date("F Y") . ' is, <strong>&pound;' . number_format(($row['Amount']/100), 2, '.', ',') . '</strong></p>';
    $message_content .= '<p>Fees are calculated using the squad your swimmers were members of on 1 ' . date("F Y") . '.</p><hr>';
    $message_content .= '<p>You can <a href="' . autoUrl("payments/statement/" . $id) . '">view a full itemised statement for this payment online</a> or <a href="' . autoUrl("payments/statement/" . $id . "/pdf") . '">download your full statement as a PDF</a>. Squad Fees for all of your swimmers are shown as one charge on statements. A breakdown of squad fees is contained in this email.</p>';

    if ($mandateInfo) {
      $message_content .= '<p>You will receive an email from our direct debit service provider GoCardless within the next three working days confirming the amount to be charged by direct debit.</p>';
    } else {
      $message_content .= '<p><strong>You have not set up a direct debit.</strong> We recommend that you do so urgently.</p>';
    }

    $email_info = [
      "user" => $row['UserID'],
      "subject" => $message_subject,
      "message" => $message_content
    ];

    $sql = "INSERT INTO notify (UserID, Status, Subject, Message, ForceSend, EmailType) VALUES (:user, 'Queued', :subject, :message, 0, 'Payments')";
    $db->prepare($sql)->execute($email_info);
  }
} catch (Exception $e) {
  // Report error by halting
  halt(500);
}
