<?php
http_response_code(403);
$pagetitle = "Error 403 - Forbidden, GDPR";
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
			<h1>You are not allowed access to the page you requested</h1>
			<p class="lead">Please ensure that you are logged in with the correct account to access this resource.</p>
			<p class="mono mb-0">Reason: EU General Data Protection Regulation</p>
			<hr>
			<p>Please try the following:</p>
			<ul>
				<li>Please ensure that you are logged in with the correct account to access this resource.</li>
				<li>You may not be authorised to access this resource. Click the <a href="javascript:history.back(1)">Back</a> button to try another link.</li>
			</ul>
			<p>HTTP Error 403 - Forbidden, GDPR.</p>
			<hr>
			<p class="mt-2">Contact our <a href="mailto:support@myswimmingclub.uk" title="Support Hotline">support hotline</a><?php if (!bool(env('IS_CLS'))) { ?>*<?php } ?> if the issue persists.</p>

      <?php if (!bool(env('IS_CLS'))) { ?>
      <p>* <a href="mailto:<?=htmlspecialchars(env('CLUB_EMAIL'))?>" title="<?=htmlspecialchars(env('CLUB_NAME'))?>">Contact your own club</a> in the first instance</p>
		</div>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render(); ?>
