<?php

$customBackground = "bg-warning";

global $e;
$reportedError = reportError($e);

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
				<li>If you are asked to confirm form resubmission, say <strong>no</strong> and press <a href="javascript:history.back(1)">Back</a> instead.</li>
				<li>If you keep seeing this error, please try again later.</li>
			</ul>
			<p>HTTP Error 500 - Internal Server Error.</p>
			<hr>
			
			<p class="mt-2">Contact our <a href="mailto:support@myswimmingclub.uk" title="Support Hotline">support hotline</a><?php if (!bool(env('IS_CLS'))) { ?>*<?php } ?> if the issue persists.</p>

      <?php if (!bool(env('IS_CLS'))) { ?>
      <p>* <a href="mailto:<?=htmlspecialchars(env('CLUB_EMAIL'))?>" title="<?=htmlspecialchars(env('CLUB_NAME'))?>">Contact your own club</a> in the first instance</p>
      <?php } ?>
		</div>
	</div>
</div>

<?php $footer = new \SDCS\Footer();
$footer->render(); ?>
