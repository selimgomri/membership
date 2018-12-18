  <?php

ignore_user_abort(true);
set_time_limit(0);

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

global $db;

$hasMandateQuery;
try {
  $sql = "SELECT * FROM `paymentMandates` INNER JOIN `paymentPreferredMandate` ON paymentMandates.UserID = paymentPreferredMandate.UserID WHERE paymentMandates.UserID = ? AND `InUse` = '1'";
  $hasMandateQuery = $db->prepare($sql);
} catch (Exception $e) {
  halt(500);
}

$date = date("Y-m") . "-01";
$day = date("d");

$sql = "SELECT `payments`.`UserID`, `Amount`, `Currency`, `Name`, `PaymentID` FROM `payments` LEFT JOIN `paymentSchedule` ON payments.UserID =
paymentSchedule.UserID WHERE (`Status` = 'pending_api_request' AND `Day` <=
'$day' AND `Type` = 'Payment') OR (`Status` = 'pending_api_request' AND `Day` IS
NULL AND `Type` = 'Payment') LIMIT 4;";
$result = mysqli_query($link, $sql);
for ($i = 0; $i < mysqli_num_rows($result); $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$userid = $row['UserID'];
  try {
    $hasMandateQuery->execute([$userid]);
  } catch (Exception $e) {
    halt(500);
  }
  $mandateInfo = $hasMandateQuery->fetch(PDO::FETCH_ASSOC);

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
    $message_content = '<p>Here are your club fees for ' . date("F Y") . '.</p>';
    $message_content .= myMonthlyFeeTable($link, $row['UserID']);
    $message_content .= '<p>This means your total fee for ' . date("F Y") . ' is, <strong>&pound;' . number_format(($row['Amount']/100), 2, '.', ',') . '</strong></p>';
    $message_content .= '<p>This total covers all of your Club Fees.</p><p>Fees are calculated using the squad your swimmers were members of on 1 ' . date("F Y") . '.</p><hr>';
    $message_content .= '<p>Don\'t forget that from February 2019, squad fees will be changing must be paid by Direct Debit. <a href="https://www.chesterlestreetasc.co.uk/2018/12/changes-to-squad-fees-from-february-2019/">Get the full details of the new fees on our website.</a></p>';
    $message_content .= '<p><strong>Signed up for Direct Debit?</strong> Remember to make sure you\'ve cancelled your standing orders!</p>';

    $email_info = [
      "user" => $row['UserID'],
      "subject" => $message_subject,
      "message" => $message_content
    ];

    $sql = "INSERT INTO notify (UserID, Status, Subject, Message, ForceSend, EmailType) VALUES (:user, 'Queued', :subject, :message, 0, 'Payments')";
    $db->prepare($sql)->execute($email_info);
  }
}
