<?

$pagetitle = "Renewal Completed";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded shadow">
		<h1>Thank you for renewing your membership</h1>
		<p class="lead">
			We'll charge you your renewal fee on or after the first day of next month.
		</p>

		<p>
			If you have further questions about membership renewal, please contact the
			membership officer by email - <a
			href="mailto:membership@chesterlestreetasc.co.uk">membership@chesterlestreetasc.co.uk</a>.
		</p>

		<p class="mb-0">
			<a href="<? echo autoUrl(""); ?>" class="btn btn-success">
				Return to Dashboard
			</a>
		</p>

	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
