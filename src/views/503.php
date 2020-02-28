<?php
http_response_code(503);
header("Retry-After: " . date('D, d M Y H:i:s e', strtotime('+24 hours')));
header("Retry-After: 86400");
$pagetitle = "Error 503 - Service Unavailable";
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
			<h1>Service Unavailable</h1>
			<p class="lead">The service is currently unavailable because of maintenance.</p>
			<hr>
			<p>Please try again later</p>
			<p>HTTP Error 503 - Service Unavailable.</p>
			<hr>
			<p class="mt-2">Contact our <a href="mailto:support@myswimmingclub.uk" title="Support Hotline">support hotline</a><?php if (!bool(env('IS_CLS'))) { ?>*<?php } ?> if the issue persists.</p>

      <?php if (!bool(env('IS_CLS'))) { ?>
      <p>* <a href="mailto:<?=htmlspecialchars(env('CLUB_EMAIL'))?>" title="<?=htmlspecialchars(env('CLUB_NAME'))?>">Contact your own club</a> in the first instance</p>
		</div>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render(); ?>
