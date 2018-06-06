<?php
header("HTTP/1.1 503 Service Unavailable");
$pagetitle = "Error 503 - Service Unavailable";
include "header.php";
?>

<div class="container">
	<h1>Service Unavailable</h1>
	<p class="lead">The service is currently unavailable because of maintenance.</p>
	<hr>
	<p>Please try again later</p>
	<p>HTTP Error 503 - Service Unavailable.</p>
	<hr>
	<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">support address</a> if the issue persists.</p>
</div>

<?php include "footer.php"; ?>
