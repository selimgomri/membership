<?php
header("HTTP/1.1 404 Not Found");
$pagetitle = "Error 404 - Page not found";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>The page you requested cannot be found</h1>
	<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>
	<hr>
	<p>Please try the following:</p>
	<ul>
		<li>Make sure that the Web site address displayed in the address bar of your browser is spelled and formatted correctly.</li>
		<li>If you reached this page by clicking a link, contact the Web site administrator to alert them that the link is incorrectly formatted.</li>
		<li>Click the <a href="javascript:history.back(1)">Back</a> button to try another link.</li>
	</ul>
	<p>HTTP Error 404 - File or directory not found.</p>
	<hr>
	<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">support address</a> if the issue persists.</p>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
