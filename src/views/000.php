<?php
http_response_code(200);
$pagetitle = "Status 200 - OK";

global $currentUser;
if ($currentUser == null) {
	include BASE_PATH . "views/head.php";
} else {
	include BASE_PATH . "views/header.php";
}
?>

<div class="container">
	<div class="row">
		<div class="col-lg-8">
			<h1>The Membership System is closed for maintenance</h1>
			<p class="lead">We'll be back shortly.</p>
			<hr>
			<p>HTTP Status 200 - OK.</p>
			<hr>
			<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">support address</a> if you need urgent access to database records. Gala entries cannot be processed at this time - We plan scheduled downtime to avoid gala entry deadlines.</p>
		</div>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
