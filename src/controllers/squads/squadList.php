<?php
$access = $_SESSION['AccessLevel'];
$pagetitle = "Squads";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php"; ?>
<div class="container">
	<div class="my-3 p-3 bg-white rounded box-shadow">
		<h1>Squad Details</h1>
		<p class="lead border-bottom border-gray pb-2 mb-3">Information about our squads</p>
		<?php echo squadInfoTable($link, true); ?>
		<?php if ($access == "Admin") { ?>
		<p class="mb-0">
			<a href="<?php echo autoUrl("squads/addsquad"); ?>" class="btn btn-outline-dark">Add a Squad</a>
		</p>
		<?php } ?>
	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
