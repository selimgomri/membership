<?php
header("HTTP/1.1 403 Forbidden");
$pagetitle = "Error 403 - Forbidden";
include "header.php";
?>

<div class="container">
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

<?php include "footer.php"; ?>
