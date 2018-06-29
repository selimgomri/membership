<?php

$user = $_SESSION['UserID'];
$pagetitle = "Notify";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
	<h1>Notify</h1>
	<p class="lead">Send Emails to targeted groups of parents</p>
	<div class="alert alert-info">
		Notify is our <strong>GDPR Compliant</strong> Email System
	</div>
  <hr>
	<p>This service <strong>must</strong> be used in moderation. Repetitive emails will be treated as spam by email services.</p>
</div>

<?php include BASE_PATH . "views/footer.php";
