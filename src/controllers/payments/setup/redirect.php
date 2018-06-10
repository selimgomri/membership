<?php
try {
	$user = $_SESSION['UserID'];
	$redirectFlowId = mysqli_real_escape_string($link, $_REQUEST['redirect_flow_id']);

	$redirectFlow = $client->redirectFlows()->complete(
	    $redirectFlowId, //The redirect flow ID from above.
	    ["params" => ["session_token" => session_id()]]
	);

	$mandate = mysqli_real_escape_string($link, $redirectFlow->links->mandate);
	$customer = mysqli_real_escape_string($link, $redirectFlow->links->customer);

	$sql = "INSERT INTO `paymentMandates` (`UserID`, `Name`, `Mandate`, `Customer`, `InUse`) VALUES ('$user', 'Mandate', '$mandate', '$customer', '1');";
	mysqli_query($link, $sql);

	// If there is no preferred mandate existing, automatically set it to the one we've just added
	$sql = "SELECT `MandateId` FROM `paymentMandates` WHERE `UserID` = '$user';";
	$result = mysqli_query($link, $sql);
	if (mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$mandateId = $row['MandateID'];
		$sql = "UPDATE `paymentPreferredMandate` SET `MandateID` = '$mandateId' WHERE `UserID` = '$user';";
		mysqli_query($link, $sql);
	}

	include BASE_PATH . "views/header.php";
	include BASE_PATH . "views/paymentsMenu.php";
	 ?>

	<div class="container">
		<h1>You've successfully set up your new direct debit.</h1>
		<p class="lead">GoCardless Ltd will appear on your bank statement when payments are taken against this Direct Debit.</p>
		<p>GoCardless Ltd handles direct debit payments for Chester-le-Street ASC. You will see <span class="mono">CHESTERLESTRE</span> as  reference for each payment.</p>
	</div>

	<?php include BASE_PATH . "views/footer.php";

}
catch (Exception $e) {
	halt(500);
}
