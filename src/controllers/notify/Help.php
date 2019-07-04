<?php

$pagetitle = "Notify";

$use_white_background = true;

include BASE_PATH . "views/header.php";
?>

<div class="container">
	<div class="row">
		<div class="col-lg-8 mb-5">
			<h1>Notify from <?=CLUB_NAME?></h1>
			<p class="lead">
				Notify is the Chester-le-Street ASC Club Digital Services member mailing
				list solution.
			</p>
			<p>
				This General Data Protection Regulation Compliant system enables rapid
				communication with our members. The  system allows us to target emails
				to parents of selected squads and supports modern email standards.
			</p>
			<p>
				To unsubscribe or resubscribe to messages sent by Notify, go to <a
				href="<?php echo autoUrl("my-account"); ?>">My Account</a>. You can also
				control your SMS Messaging preferences there.
			</p>
      <p>
        Many emails will also come with an unsubscribe link at the end, though
        mandatory information emails won't.
      </p>
		</div>
	</div>
</div>

<?php

include BASE_PATH . "views/footer.php";
