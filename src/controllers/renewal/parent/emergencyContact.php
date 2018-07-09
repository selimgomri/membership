<?php

$user = $_SESSION['UserID'];

$contacts = new EmergencyContacts($link);
$contacts->byParent($user);

$contactsArray = $contacts->getContacts();

$pagetitle = "Emergency Contacts";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded box-shadow">
		<? if (isset($_SESSION['ErrorState'])) {
			echo $_SESSION['ErrorState'];
			unset($_SESSION['ErrorState']);
			?><hr><?
		} ?>
		<form method="post">
			<h1>Emergency Contacts</h1>
			<p class="lead">These are your emergency contacts.</p>

			<p class="border-bottom border-gray pb-2 mb-0">
				We'll use these emergency contacts for all swimmers connected to your
				account if we can't reach you on your phone number. You can change your
				phone number in <a href="<? echo autoUrl("myaccount"); ?>">My Account</a>
			</p>

			<div class="mb-3">
			<? for ($i = 0; $i < sizeof($contactsArray); $i++) {
				?>
				<div class="media pt-3">
					<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
						<p class="mb-0">
							<strong class="d-block">
								<? echo $contactsArray[$i]->getName(); ?>
							</strong>
							<a href="tel:<? echo $contactsArray[$i]->getContactNumber(); ?>">
								<? echo $contactsArray[$i]->getContactNumber(); ?>
							</a>
						</p>
					</div>
				</div>
				<?
			} ?>
			</div>

			<p>
				Head to the <a href="<? echo autoUrl("emergencycontacts"); ?>"
				target="_blank">Emergency Contacts section</a> to add to or edit your
				emergency contacts
			</p>

			<div>
				<button type="submit" class="btn btn-success">Save and Continue</button>
			</div>
		</form>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
