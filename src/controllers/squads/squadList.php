<?php
$access = $_SESSION['AccessLevel'];
$pagetitle = "Squads";
include BASE_PATH . "views/header.php"; ?>
<div class="container">
<h1>Squad Details</h1>
<p class="lead">Information about our squads</p>
<?php echo squadInfoTable($link, true); ?>
<?php if ($access == "Admin") { ?>
<p>
	<a href="<?php echo autoUrl("squads/addsquad"); ?>" class="btn btn-outline-dark">Add a Squad</a>
</p>
<?php } ?>
</div>
<?php include BASE_PATH . "views/footer.php";
