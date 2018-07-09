<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Swimmer Review";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded box-shadow">
		<form method="post">
			<? if (isset($_SESSION['ErrorState'])) {
				echo $_SESSION['ErrorState'];
				unset($_SESSION['ErrorState']);
				?><hr><?
			} ?>
			<h1>Review your swimmers</h1>
			<p class="lead">
				Make sure all of your swimmers are listed here. Make sure you
				<a target="_blank" href="<? echo autoUrl("myaccount/addswimmer"); ?>">
					add them
				</a>
				if not.
			</p>

			<? echo mySwimmersTable($link, $userID); ?>

			<div>
				<button type="submit" class="btn btn-success">Save and Continue</button>
			</div>
		</form>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
