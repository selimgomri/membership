<?php

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

$uSql = "SELECT * FROM `users` WHERE `UserID` = '$user';";
$uRes = mysqli_query($link, $uSql);
$uRow = mysqli_fetch_array($uRes, MYSQLI_ASSOC);

$contacts = new EmergencyContacts($link);
$contacts->byParent($user);

$contactsArray = $contacts->getContacts();

$pagetitle = "Emergency Contacts";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
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

			<? if (user_needs_registration($user)) { ?>
				<p class="border-bottom border-gray pb-2 mb-0">
					We'll use these emergency contacts for all swimmers connected to your
					account if we can't reach you on your phone number. You will be able
					to change your phone number at any time in My Account, once you've
					finished registration.</a>
				</p>
			<? } else { ?>
			<p class="border-bottom border-gray pb-2 mb-0">
				We'll use these emergency contacts for all swimmers connected to your
				account if we can't reach you on your phone number. You can change your
				phone number in <a href="<? echo autoUrl("myaccount"); ?>">My Account</a>
			</p>
			<? } ?>

			<div class="mb-3">
        <div class="media pt-3">
  				<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
						<p class="mb-0">
							<strong class="d-block">
								<? echo $uRow['Forename'] . " " . $uRow['Surname']; ?> (From My
								Account)
							</strong>
							<a href="tel:<? echo $uRow['Mobile']; ?>">
								<? echo $uRow['Mobile']; ?>
							</a>
						</p>
  				</div>
  			</div>
			<? for ($i = 0; $i < sizeof($contactsArray); $i++) {
				?>
				<div class="media pt-3">
					<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
						<div class="row align-items-center">
							<div class="col-9">
								<p class="mb-0">
									<strong class="d-block">
										<? echo $contactsArray[$i]->getName(); ?>
									</strong>
									<a href="tel:<? echo $contactsArray[$i]->getContactNumber(); ?>">
										<? echo $contactsArray[$i]->getContactNumber(); ?>
									</a>
								</p>
							</div>
							<div class="col text-right">
								<a href="<? echo autoUrl("renewal/emergencycontacts/edit/" .
								$contactsArray[$i]->getID()); ?>" class="btn btn-primary">
									Edit
								</a>
							</div>
						</div>
					</div>
				</div>
				<?
			} ?>
			</div>

			<p class="mb-0">
				<a href="<?= autoUrl("renewal/emergencycontacts/new") ?>" class="btn btn-secondary">Add a New Contact</a>
			</p>

			<hr>

			<p>
				Ready to move on?
			</p>
			<p class="mb-0">
				<button type="submit" class="btn btn-success">Save and Continue</button>
			</p>
		</form>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
