<?php
$userID = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$pagetitle = "Fee Review";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container-xl">
	<main class="">
		<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
			echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
			unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
		?>
			<hr><?php
				} ?>
		<h1>Your Fees</h1>
		<form method="post">
			<p class="lead">Here are the monthly fees you pay.</p>

			<div class="mb-3">
				<?= myMonthlyFeeTable(null, $userID) ?>
			</div>

			<?php if (app()->tenant->getBooleanKey('ENABLE_BILLING_SYSTEM')) { ?>
				<p>You will pay these fees by Direct Debit*.</p>

				<p class="small">* Where supported by your club.</p>
			<?php } else { ?>
				<p>
					Your club has disabled the automated billing systems provided as part of the membership system. Your club will tell you how they want you to pay these fees.
				</p>

				<p>
					As your club isn't using automated billing, these fees shown above may not always be accurate.
				</p>
			<?php } ?>

			<div>
				<button type="submit" class="btn btn-success">Save and Continue</button>
			</div>
		</form>
	</main>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
