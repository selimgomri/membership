<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Fee Review";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<? if (isset($_SESSION['ErrorState'])) {
		echo $_SESSION['ErrorState'];
		unset($_SESSION['ErrorState']);
		?><hr><?
	} ?>
	<h1>Your Fees</h1>
	<form method="post">
		<p class="lead">Here are the monthly fees you pay.</p>

		<div class="mb-3">
			<? echo myMonthlyFeeTable($link, $userID); ?>
		</div>

		<p>You will pay these fees by Direct Debit. You will also pay for Galas and
		other fees by Direct Debit whenever nessecary.</p>

		<div class="mb-3">
			<a class="btn btn-outline-success" href="">Save</a>
			<button type="submit" class="btn btn-success">Save and Continue</button>
		</div>
	</form>
</div>

<?php include BASE_PATH . "views/footer.php";
