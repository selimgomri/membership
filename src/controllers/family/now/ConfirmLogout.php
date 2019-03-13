<?

$pagetitle = "Confirm Logout";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded shadow">
		<h1>You are about to be logged out.</h1>
		<p>To continue with parental registration we need to log you out of your
		account.</p>

		<p>
			You won't lose any data, nor will the parent with you be able to see any
			sensitive data during the process.
		</p>

		<p class="mb-0">
			<a target="_self" class="btn btn-dark" href="<?php echo autoUrl("family/register/later/" . $id); ?>">
				Cancel
			</a>
			<a target="_self" class="btn btn-secondary" href="<?php echo autoUrl("family/register/now/" . $id . "/go"); ?>">
				Continue
			</a>
		</p>

	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
