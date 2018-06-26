<?php
$pagetitle = "Emergency Contacts";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<? if (isset($_SESSION['ErrorState'])) {
		echo $_SESSION['ErrorState'];
		unset($_SESSION['ErrorState']);
		?><hr><?
	} ?>
	<form method="post">
		<h1>Emergency Contacts</h1>
		<p class="lead">These are your emergency contacts.</p>

		<p>These are secondary contacts</p>

		<div class="mb-3">
			<a class="btn btn-outline-success" href="">Save</a>
			<button type="submit" class="btn btn-success">Save and Continue</button>
		</div>
	</form>
</div>

<?php include BASE_PATH . "views/footer.php";
