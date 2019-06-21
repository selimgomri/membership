<?php
http_response_code(403);
$pagetitle = "Error 403 - Forbidden";
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
			<h1>You are not allowed access to the page you requested</h1>
			<p class="lead">Please ensure that you are logged in with the correct account to access this resource.</p>
			<hr>
			<p>Please try the following:</p>
			<ul>
				<li>Please ensure that you are logged in with the correct account to access this resource.</li>
				<li>You may not be authorised to access this resource. Click the <a href="javascript:history.back(1)">Back</a> button to try another link.</li>
			</ul>
			<p>HTTP Error 403 - Forbidden.</p>
			<hr>
			<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">support address</a> if the issue persists.</p>
		</div>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
