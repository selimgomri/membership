<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Fee Review";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Review your fees</h1>
	<p class="lead">Check the fees for your squads are right</p>

	<? echo myMonthlyFeeTable($link, $userID); ?>

	<div class="mb-3">
		<a class="btn btn-outline-success" href="">Save</a>
		<a class="btn btn-success" href="">Save and Continue</a>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
