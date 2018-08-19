<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Swimmer Review";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
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

			<p>
				If your swimmers are not listed here, their membership cannot be
				renewed. This will lead to a lapse in their club and ASA membership.
				Your swimmers will no longer be insured and automatically removed from
				our registers if they cannot renew.
			</p>

			<? echo mySwimmersTable($link, $userID); ?>

			<? if (user_needs_registration($user)) { ?>
				<p>
					The links to your swimmers are unavailable until you have completed
					registration.
				</p>
			<? } ?>

			<div>
				<button type="submit" class="btn btn-success">Save and Continue</button>
			</div>
		</form>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
