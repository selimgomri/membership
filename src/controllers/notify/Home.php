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
  <h2>How to Use Notify</h2>
  <p>
    Notify is available to Squad Coaches and System Administrators and allows
    you to send an email to parents of selected groups of swimmers. It is
    possible to create a custom group of swimmers (called a Targeted List), or
    to use a squad. You can send to any combination of squads and lists just by
    ticking boxes.
  </p>
  <p>
    Click on "Notify Composer" to write an email message. All emails sent will
    be personally addressed to each parent who reciceves them.
  </p>
  <p>
    Emails are added to a queue to be sent. It could take up to thirty minutes
    to send an email to all parents in the club. Parents who have not opted in
    to recieving emails will not recieve messages.
  </p>
</div>

<?php include BASE_PATH . "views/footer.php";
