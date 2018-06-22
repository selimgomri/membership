<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Fee Review";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Your Fees</h1>
	<p class="lead">Here are the monthly fees you pay.</p>

	<div class="mb-3">
		<? echo myMonthlyFeeTable($link, $userID); ?>
	</div>

	<p>You will pay these fees by Direct Debit. You will also pay for Galas and
	other fees by Direct Debit whenever nessecary.</p>

	<div class="mb-3">
		<a class="btn btn-outline-success" href="">Save</a>
		<a class="btn btn-success" href="">Save and Continue</a>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
