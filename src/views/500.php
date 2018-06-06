<?php
header("HTTP/1.1 500 Internal Server Error");
$pagetitle = "Error 500 - Internal Server Error";
include "header.php";
?>

<div class="container">
	<h1>Internal Server Error</h1>
	<p class="lead">Something went wrong so we are unable to serve you this page. We're sorry that this has occured.</p>
	<hr>
	<p>Please try the following:</p>
	<ul>
		<li>If this is the first time you have seen this error, try reloading the page.</li>
		<li>If you keep seeing this error, please try again later.</li>
	</ul>
	<p>HTTP Error 500 - Internal Server Error.</p>
	<hr>
	<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">support address</a> if the issue persists.</p>
</div>

<?php include "footer.php"; ?>
