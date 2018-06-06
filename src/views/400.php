<?php
header("HTTP/1.1 400 Bad Request");
$pagetitle = "Error 400 - Bad Request";
include "header.php";
?>

<div class="container">
	<h1>We were unable to process your request</h1>
	<p class="lead">There was a problem with the request sent by your browser. This may be due to an issue with your access permissions.</p>
	<hr>
	<p>Please try the following:</p>
	<ul>
		<li>Make sure that the Web site address displayed in the address bar of your browser is spelled and formatted correctly.</li>
		<li>If you reached this page by clicking a link, contact the Web site administrator to alert them that the link is incorrectly formatted.</li>
		<li>Click the <a href="javascript:history.back(1)">Back</a> button to try another link.</li>
	</ul>
	<p>HTTP Error 400 - Bad Request.</p>
	<hr>
	<p class="mt-2">Contact our <a href="mailto:support@chesterlestreetasc.co.uk" title="Support Hotline">support address</a> if the issue persists.</p>
</div>

<?php include "footer.php"; ?>
