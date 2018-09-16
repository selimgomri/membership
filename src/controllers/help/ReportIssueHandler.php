<?php

use Respect\Validation\Validator as v;

$target = urldecode($_GET['url']);

$pagetitle = "Report an Issue";
include BASE_PATH . 'views/header.php'; ?>

<div class="container">
	<h1>Report a Website Issue</h1>
	<? if ($_SESSION['ErrorReportStatus'] == true) { ?>
		<p>We have reported that page to our team.</p>
		<p>Thank you for your feedback. It really helps us improve our website.</p>
		<p>
			<a href="<?=htmlspecialchars($_SESSION['ErrorReportTarget'])?>" class="btn btn-secondary">
				Return to Page
			</a>
		</p>
	<? unset($_SESSION['ErrorReportTarget']); ?>
	<? } else if (!isset($_GET['url']) || (isset($_SESSION['ErrorReportStatus']) &&
	$_SESSION['ErrorReportStatus'] == false)) { ?>
		<p>We were unable to report that page. You may have not provided a URL or
		the URL was malformed.</p>
		<p>
			<a href="https://www.chesterlestreetasc.co.uk" class="btn btn-secondary">
				Return to Home
			</a>
		</p>
	<? } else { ?>
		<p>Let us know what's wrong with the page so that we can fix it as quickly as possible.</p>
		<form method="post" action="<?=app('request')->curl?>">
			<div class="form-group">
		    <label for="report_url">Page Address</label>
		    <input type="url" value="<?=htmlspecialchars($target)?>" readonly class="form-control" id="report_url" name="report_url">
		  </div>
			<div class="form-group">
		    <label for="Message">What's Wrong?</label>
		    <textarea class="form-control" id="Message" name="Message" rows="3" aria-describedby="MHelp"></textarea>
				<small id="MHelp" class="form-text text-muted">You don't need to fill out this box if you don't want to</small>
		  </div>
			<p>
				<button class="btn btn-dark" type="submit">
					Report Error
				</button>
				<a href="<?=htmlspecialchars($target)?>" class="btn btn-danger">
					Cancel
				</a>
			</p>
		</form>
	<? } ?>
</div>

<?

if (isset($_SESSION['ErrorReportStatus'])) {
	unset($_SESSION['ErrorReportStatus']);
}
include BASE_PATH . 'views/footer.php';
