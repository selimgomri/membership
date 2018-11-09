<?php

$user = $_SESSION['UserID'];
$pagetitle = "Notify";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
	<h1>Notify</h1>
	<p class="lead">Send Emails to targeted groups of parents</p>
  <? if (isset($_SESSION['NotifySuccess'])) { ?>
  <div class="alert alert-success">
		We've successfully queued your email. <?=
		$_SESSION['NotifySuccess']['Count'] ?> people will recieve your message<? if
		(!$_SESSION['NotifySuccess']['Force']) { ?> if they have opted in to
		recieving emails from us<? } ?>.
	</div>
  <? }
  unset($_SESSION['NotifySuccess']); ?>
	<div class="alert alert-info">
		Notify is our <strong>GDPR Compliant</strong> Email System
	</div>
  <hr>
	<p>
    This service <strong>must</strong> be used in moderation. Repetitive emails
    will be treated as spam by email services.
  </p>
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
  <p>
    <strong>
      Please be aware:
    </strong>
    If you Force Send an email, you will be contacted by the System Administrator
    and asked to justify why you did so. This is because of our obligations under
    the GDPR rules. Acceptable use of Force Send includes;
  </p>
  <ul>
    <li>Alerting parents that sessions have been cancelled</li>
    <li>Sending important gala updates</li>
    <li>Contacting squad parents in an emergency</li>
  </ul>
  <p>
    Other use cases are allowed but must be justifiable in terms of our
    operational needs.
  </p>
  <p class="small">
    Provided by Chester-le-Street ASC Club Digital Services.
  </p>
</div>

<?php include BASE_PATH . "views/footer.php";
