<?php
if (isset($id)) {
	$id = mysqli_real_escape_string($link, $id);
	include BASE_PATH . "views/header.php";
	?>
	<div class="container">
		<h1>Code of Conduct Acceptance</h1>
		<p class="lead">For {<? echo $id ?>}</p>

		<div class="mb-3">
			<a class="btn btn-outline-success" href="">Save</a>
			<a class="btn btn-success" href="">Save and Continue</a>
		</div>
	</div>

	<?php include BASE_PATH . "views/footer.php";
}
else {
$pagetitle = "Code of Conduct Acceptance";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Code of Conduct Acceptance</h1>
	<p class="lead">For You</p>

	<div class="mb-3">
		<a class="btn btn-outline-success" href="">Save</a>
		<a class="btn btn-success" href="">Save and Continue</a>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
}
