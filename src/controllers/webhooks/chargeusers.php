<?php

ignore_user_abort(true);
set_time_limit(0);

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

$date = date("Y-m") . "-01";
$day = date("d");

$sql = "SELECT * FROM `payments` LEFT JOIN `paymentSchedule` ON payments.UserID = paymentSchedule.UserID WHERE `Status` = 'pending_api_request' AND `Day` <= '$day' AND `Type` = 'Payment' LIMIT 4;";
$result = mysqli_query($link, $sql);
for ($i = 0; $i < mysqli_num_rows($result); $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$userid = $row['UserID'];
	$sql = "SELECT * FROM `paymentMandates` INNER JOIN `paymentPreferredMandate` ON paymentMandates.UserID = paymentPreferredMandate.UserID WHERE paymentMandates.UserID = '$userid' AND `InUse` = '1';";
  if (mysqli_num_rows(mysqli_query($link, $sql)) != 0) {
  	$mandateInfo = mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC);

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
  		$status = $payment->status;

  		$sql = "UPDATE `payments` SET `Status` = '$status', `MandateID` = '$mandateid', `PMkey` = '$id' WHERE `PaymentID` = '$paymentID';";
  		mysqli_query($link, $sql);

  		$sql = "UPDATE `paymentsPending` SET `Status` = 'Requested', `PMkey` = '$id' WHERE `UserID` = '$userid' AND `Status` = 'Queued' AND `Type` = 'Payment' AND `Date` <= '$date';";
  		mysqli_query($link, $sql);

  	} catch (Exception $e) {
  		halt(500);
  	}
  } else {
    $paymentID = $row['PaymentID'];
    $id = "CASH" . $paymentID;
    $sql = "UPDATE `payments` SET `Status` = 'cust_not_dd', `MandateID` = 'CASH', `PMkey` = '$id' WHERE `PaymentID` = '$paymentID';";
    mysqli_query($link, $sql);

    $sql = "UPDATE `paymentsPending` SET `Status` = 'Paid', `PMkey` = '$id' WHERE `UserID` = '$userid' AND `Status` = 'Queued' AND `Type` = 'Payment' AND `Date` <= '$date';";
    mysqli_query($link, $sql);

    $message_subject = "Your Monthly Fee for " . date("F Y");
    $message_content = '
    <p>Here is your fee for ' . date("F Y") . '.</p>
    <p>&pound;' . number_format(($amount/10), 2, '.', ',') . '</p>
    <p>This total covers all of your Squad and CrossFit Fees. Please remember that you may pay CrossFit fees in a separate Standing Order.</p>
    <p>Fees are calculated on the squad your swimmers were members of on 1 ' . date("F Y") . '.</p>
    <hr>
    <p>Notice: We do not currently have full records detailing which swimmers take part in CrossFit in our online systems. This information may not be correct at the moment. Your fee will also only be accurate if you have connected all of your swimmers to your account.</p>
    ';

    $email_info = [
      "user" => $userid,
      "subject" => $message_subject,
      "message" = > $message_content
    ];

    $sql = "INSERT INTO notify (UserID, Status, Subject, Message, ForceSend, EmailType) VALUES (:user, 'Queued', :subject, :message, 1, 'Payments')";
    $pdo->prepare($sql)->execute($email_info);
  }
}
