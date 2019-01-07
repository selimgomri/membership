<?php
$use_white_background = true;

$access = $_SESSION['AccessLevel'];
$pagetitle = "Squads";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php"; ?>
<div class="container">
	<div class="">
		<h1>Squad Details</h1>
		<p class="lead">Information about our squads</p>
		<?php echo squadInfoTable($link, true); ?>
		<?php if ($access == "Admin") { ?>
		<p class="mb-0">
			<a href="<?php echo autoUrl("squads/addsquad"); ?>" class="btn btn-success">Add a Squad</a>
		</p>
		<?php } ?>
	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
