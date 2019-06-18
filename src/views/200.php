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
	<h1>Status OK</h1>
	<p class="lead">This is a response to let you know the request was processed correctly. We cannot tell you more information in line with our data protection obligations.</p>
	<hr>
	<p>API Incident, Errors or Downtime? Contact the support team urgently.</p>
	<p>HTTP Status 200 - OK.</p>
	<hr>
	<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">Emergency Support Hotline</a> if there are API errors despite this 200 OK Status.</p>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
