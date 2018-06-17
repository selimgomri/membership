<?php

$pagetitle = "Notify";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<div class="row">
		<div class="col-lg-8">
			<h1>Notify from Chester-le-Street ASC</h1>
			<p class="lead">
				Notify is our member mailing list solution.
			</p>
			<p>
				We've introduced this new, General Data Protection Regulation Compliant
				system to improve our communication with members. The new system allows
				us to target emails to parents of selected squads and supports modern
				standards (as specified in technical documents called RFCs) to enable
				easy unsubscribing.
			</p>
			<p>
				It's likely that you have reached this page today because these modern
				standards allow us to attach a link to our Notify help pages within
				the emails that we send, though you must be using an email client which
				supports the "List" headers.
			</p>
			<p>
				To unsubscribe or resubscribe to messages sent by Notify, go to <a
				href="<? echo autoUrl("myaccount"); ?>">My Account</a>. You can also
				control your SMS Messaging preferences there.
			</p>
		</div>
	</div>
</div>

<?

include BASE_PATH . "views/footer.php";
