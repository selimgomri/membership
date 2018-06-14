<?php

ignore_user_abort(true);
set_time_limit(0);

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

$day = date("d");

$sql = "SELECT * FROM `payments` INNER JOIN `paymentSchedule` ON payments.UserID = paymentSchedule.UserID WHERE `Status` = 'pending_api_request' AND `Day` <= '$day' AND `Type` = 'Payment';";
$result = mysqli_query($link, $sql);
for ($i = 0; $i < mysqli_num_rows($result); $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$userid = $row['UserID'];
	$sql = "SELECT * FROM `paymentMandates` INNER JOIN `paymentPreferredMandate` ON paymentMandates.UserID = paymentPreferredMandate.UserID WHERE paymentMandates.UserID = '$userid' AND `InUse` = '1';";
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

		$sql = "UPDATE `payments` SET (`MandateID` = '$mandateid', `PMkey` = '$id' WHERE `PaymentID`) = '$paymentID';";
		mysqli_query($link, $sql);
	} catch (Exception $e) {
		halt(500);
	}
}
