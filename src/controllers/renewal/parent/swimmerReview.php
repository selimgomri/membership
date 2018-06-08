<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Swimmer Review";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Review your swimmers</h1>
	<p class="lead">Make sure all of your swimmers are listed here. Make sure you <a href="<? echo autoUrl("myaccount/addswimmer"); ?>">add them</a> if not.</p>

	<? echo mySwimmersTable($link, $userID); ?>

	<div class="mb-3">
		<a class="btn btn-outline-success" href="">Save</a>
		<a class="btn btn-success" href="">Save and Continue</a>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
