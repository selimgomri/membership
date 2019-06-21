<?php

$customBackground = "bg-warning";

$reportedError = false;
if (env('ERROR_REPORTING_EMAIL') != null) {
	try {
		$emailMessage = '<p>This is an error report</p>';
		if (isset($e)) {
			ob_start();
			pre($e);
			$error = ob_get_clean();
			$emailMessage .= $error;
		}

		ob_start();
		pre(app('request'));
		$error = ob_get_clean();
		$emailMessage .= $error;

		notifySend(null, 'System Error Report', $emailMessage, "System Admin", env('ERROR_REPORTING_EMAIL'));
		$reportedError = true;
	} catch (Exception $f) {
		$reportedError = false;
	}
}

http_response_code(500);
$pagetitle = "Error 500 - Internal Server Error";
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
			<h1>Internal Server Error</h1>
			<p class="lead">Something went wrong so we are unable to serve you this page. We're sorry that this has occured.</p>
			<?php if ($reportedError) { ?>
			<p>
				<strong>
					Full details of this error have been reported automatically to your system administrator.
				</strong>
			</p>
			<?php } ?>
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
	</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
