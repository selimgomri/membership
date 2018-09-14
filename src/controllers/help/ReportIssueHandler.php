<?php

use Respect\Validation\Validator as v;

$target = urldecode($_GET['url']);

$display = false;

if (v::url()->validate($target)) {

	$message = "<p>An error has been reported on the following page: " . $target . ".</p>";
	$message .= "<p>Reported on " . date("l j F Y") . ".</p>";
	$message .= "<p>Sent Automatically by CLS ASC.</p>";

	notifySend("", "Website Error Report", $message, "Website Admin Team", "web@chesterlestreetasc.co.uk", ["Email" => "issues@web.service.chesterlestreetasc.co.uk", "Name" => "Chester-le-Street ASC"]);

	$display = true;

}

$pagetitle = "Report an Issue";
include BASE_PATH . 'views/header.php'; ?>

<div class="container">
	<h1>Report a Website Issue</h1>
	<? if ($display) { ?>
		<p>We have reported that page to our team</p>
		<p>
			<a href="<?=htmlspecialchars($target)?>" class="btn btn-secondary">
				Return to Page
			</a>
		</p>
	<? } else { ?>
		<p>We were unable to report that page. You may have not provided a URL or
		the URL was malformed.</p>
		<p>
			<a href="https://www.chesterlestreetasc.co.uk" class="btn btn-secondary">
				Return to Home
			</a>
		</p>
	<? } ?>
</div>

<? include BASE_PATH . 'views/footer.php';
