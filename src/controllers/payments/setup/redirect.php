<?php

$user = $_SESSION['UserID'];

$sql = "SELECT * FROM `paymentSchedule` WHERE `UserID` = '$user';";
$scheduleExists = mysqli_num_rows(mysqli_query($link, $sql));
if ($scheduleExists == 0) {
	header("Location: " . autoUrl("payments/setup/0"));
} else {
	try {
		$redirectFlowId = mysqli_real_escape_string($link, $_REQUEST['redirect_flow_id']);

		$redirectFlow = $client->redirectFlows()->complete(
		    $redirectFlowId, //The redirect flow ID from above.
		    ["params" => ["session_token" => session_id()]]
		);

		$mandate = mysqli_real_escape_string($link, $redirectFlow->links->mandate);
		$customer = mysqli_real_escape_string($link, $redirectFlow->links->customer);
	  $bankAccount = mysqli_real_escape_string($link, $redirectFlow->links->customer_bank_account);

	  $bank = $client->customerBankAccounts()->get($bankAccount);
	  $accHolderName = mysqli_real_escape_string($link, $bank->account_holder_name);
	  $accNumEnd = mysqli_real_escape_string($link, $bank->account_number_ending);
	  $bankName = mysqli_real_escape_string($link, $bank->bank_name);

		$sql1 = "INSERT INTO `paymentMandates` (`UserID`, `Name`, `Mandate`, `Customer`, `BankAccount`, `BankName`, `AccountHolderName`, `AccountNumEnd`, `InUse`) VALUES ('$user', 'Mandate', '$mandate', '$customer', '$bankAccount', '$bankName', '$accHolderName', '$accNumEnd', '1');";
		mysqli_query($link, $sql1);

		// If there is no preferred mandate existing, automatically set it to the one we've just added
		$sql = "SELECT * FROM `paymentPreferredMandate` WHERE `UserID` = '$user';";
		$count = mysqli_num_rows(mysqli_query($link, $sql));
		if ($count < 1) {
			$sql = "SELECT `MandateID` FROM `paymentMandates` WHERE `UserID` = '$user';";
			$result = mysqli_query($link, $sql);
			if (mysqli_num_rows($result) == 1) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$mandateId = $row['MandateID'];
				$sql = "INSERT INTO `paymentPreferredMandate` (`MandateID`, `UserID`) VALUES ('$mandateId', '$user');";
				mysqli_query($link, $sql);
			}
		}


		$pagetitle = "You've setup a Direct Debit";

		include BASE_PATH . "views/header.php";
		include BASE_PATH . "views/paymentsMenu.php";
		 ?>

		<div class="container">
			<h1>You've successfully set up your new direct debit.</h1>
			<p class="lead">GoCardless Ltd will appear on your bank statement when payments are taken against this Direct Debit.</p>
			<p>GoCardless Ltd handles direct debit payments for Chester-le-Street ASC. You will see <span class="mono">CHESTERLESTRE</span> as the start of the reference for each payment.</p>
			<a href="<? echo autoUrl("payments"); ?>" class="mb-3 btn btn-dark">Go to Payments</a>
		</div>

		<?php include BASE_PATH . "views/footer.php";

	}
	catch (Exception $e) {
		halt(500);
	}
}
