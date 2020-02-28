<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Fee Review";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<main class="">
		<?php if (isset($_SESSION['ErrorState'])) {
			echo $_SESSION['ErrorState'];
			unset($_SESSION['ErrorState']);
			?><hr><?php
		} ?>
		<h1>Your Fees</h1>
		<form method="post">
			<p class="lead">Here are the monthly fees you pay.</p>

			<div class="mb-3">
				<?= myMonthlyFeeTable(null, $userID) ?>
			</div>

			<p>You will pay these fees by Direct Debit.</p>

			<div>
				<button type="submit" class="btn btn-success">Save and Continue</button>
			</div>
		</form>
	</main>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
