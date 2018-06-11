<?php
$userID = $_SESSION['UserID'];
$pagetitle = "Galas you've entered";
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
	<h1>My Gala Entries</h1>
	<?php echo enteredGalas($link, $userID); ?>
	<p class="mt-2">Gala entries shown above do not confirm accepted entries.</p>
</div>
<?php include BASE_PATH . "views/footer.php";
