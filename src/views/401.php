<?php
http_response_code(401);
$pagetitle = "Error 401 - Unauthorised";
global $currentUser;
if ($currentUser == null) {
	include BASE_PATH . "views/head.php";
} else {
	include BASE_PATH . "views/header.php";
}
?>

<div class="container">
	<h1>Unauthorised</h1>
	<p class="lead">Authentication has failed or has not yet been provided.</p>
	<hr>
	<p>Please try the following:</p>
	<ul>
		<li>Ensure you have logged in if required.</li>
		<li>Ensure you have logged in with an account which has access permissions for this resource.</li>
		<li>Click the <a href="javascript:history.back(1)">Back</a> button to try another link.</li>
	</ul>
	<p>HTTP Error 401 - Unauthorised.</p>
	<hr>
	<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">support address</a> if the issue persists.</p>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
