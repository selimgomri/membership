<?php

global $db;

$user = $_SESSION['UserID'];

$selectSchedule = $db->prepare("SELECT COUNT(*) FROM `paymentSchedule` WHERE `UserID` = ?");
$selectSchedule->execute([$_SESSION['UserID']]);

if ($selectSchedule->fetchColumn() == 0) {
	header("Location: " . autoUrl("payments/setup/0"));
} else {
	try {
		$redirectFlowId = $_REQUEST['redirect_flow_id'];
		$redirectFlow = $client->redirectFlows()->complete(
		    $_SESSION['GC_REDIRECTFLOW_ID'], //The redirect flow ID from above.
		    ["params" => ["session_token" => $_SESSION['Token']]]
		);

		$mandate = $redirectFlow->links->mandate;
		$customer = $redirectFlow->links->customer;
	  $bankAccount = $redirectFlow->links->customer_bank_account;

	  $bank = $client->customerBankAccounts()->get($bankAccount);
	  $accHolderName = $bank->account_holder_name;
	  $accNumEnd = $bank->account_number_ending;
	  $bankName = $bank->bank_name;

    $insertToMandates = $db->prepare("INSERT INTO `paymentMandates` (`UserID`, `Name`, `Mandate`, `Customer`, `BankAccount`, `BankName`, `AccountHolderName`, `AccountNumEnd`, `InUse`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertToMandates->execute([
      $_SESSION['UserID'],
      'Mandate',
      $mandate,
      $customer,
      $bankAccount,
      $bankName,
      $accHolderName,
      $accNumEnd,
      true
    ]);

		// If there is no preferred mandate existing, automatically set it to the one we've just added
		$getPreferredCount = $db->prepare("SELECT COUNT(*) FROM `paymentPreferredMandate` WHERE `UserID` = ?");
    $getPreferredCount->execute([$_SESSION['UserID']]);
		if ($getPreferredCount->fetchColumn() == 0) {
      $getMandateId = $db->prepare("SELECT `MandateID` FROM `paymentMandates` WHERE `Mandate` = ?");
      $getMandateId->execute([$mandate]);
			if ($mandateId = $getMandateId->fetchColumn()) {
        $insertPreferred = $db->prepare("INSERT INTO `paymentPreferredMandate` (`MandateID`, `UserID`) VALUES (?, ?)");
        $insertPreferred->execute([$mandateId, $user]);
			}
		}

    $use_white_background = true;
		$pagetitle = "You've setup a Direct Debit";

		include BASE_PATH . "views/header.php";
		include BASE_PATH . "views/paymentsMenu.php";
		 ?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>You've successfully set up your new direct debit.</h1>
      <p class="lead">GoCardless will appear on your bank statement when
        payments are taken against this Direct Debit.</p>
      <p>GoCardless handles direct debit payments for <?=htmlspecialchars(env('CLUB_NAME'))?>.</p>
      <?php if (isset($renewal_trap) && $renewal_trap) { ?>
      <a href="<?php echo autoUrl("renewal/go"); ?>" class="mb-3 btn btn-success">Continue registration or renewal</a>
      <?php } else { ?>
      <a href="<?php echo autoUrl("payments"); ?>" class="mb-3 btn btn-dark">Go to Payments</a>
      <?php } ?>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";

	}
	catch (Exception $e) {
		halt(500);
	}
}