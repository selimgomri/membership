<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$db = app()->db;

$userInfo = $db->prepare("SELECT Forename, Surname, Mobile FROM `users` WHERE `UserID` = ?");
$userInfo->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$user = $userInfo->fetch(PDO::FETCH_ASSOC);

$mobile = PhoneNumber::parse($user['Mobile'], 'GB');

$contacts = new EmergencyContacts($db);
$contacts->byParent($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']);

$contactsArray = $contacts->getContacts();

$pagetitle = "Emergency Contacts";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<div class="">
		<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
			echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
			unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
			?><hr><?php
		} ?>
		<form method="post">
			<h1>Emergency Contacts</h1>
			<p class="lead">These are your emergency contacts.</p>

			<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AddNewSuccess'])) {
			echo $_SESSION['TENANT-' . app()->tenant->getId()]['AddNewSuccess'];
			unset($_SESSION['TENANT-' . app()->tenant->getId()]['AddNewSuccess']);
		} ?>

			<?php if (user_needs_registration($_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) { ?>
				<p class="border-bottom border-gray pb-3 mb-0">
					We'll use these emergency contacts for all members connected to your
					account if we can't reach you on your phone number. You will be able
					to change your phone number and emergency contacts at any time, once you've
					finished registration.</a>
				</p>
			<?php } else { ?>
			<p class="border-bottom border-gray pb-3 mb-0">
				We'll use these emergency contacts for all swimmers connected to your
				account if we can't reach you on your phone number. You can change your
				phone number in <a href="<?=autoUrl("my-account")?>">My Account</a>
			</p>
			<?php } ?>

			<div class="mb-3">
        <div class="media pt-3">
  				<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
						<p class="mb-0">
							<strong>
								<?=htmlspecialchars($user['Forename'] . " " . $user['Surname'])?>
							</strong>
							<em>(From My Account)</em>
						</p>
						<p class="mb-0">
							<a href="tel:<?=$mobile->format(PhoneNumberFormat::RFC3966)?>">
								<?=$mobile->format(PhoneNumberFormat::NATIONAL)?>
							</a>
						</p>
  				</div>
  			</div>
			<?php for ($i = 0; $i < sizeof($contactsArray); $i++) {
				?>
				<div class="media pt-3">
					<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
						<div class="row align-items-center">
							<div class="col-9">
								<p class="mb-0">
									<strong>
										<?=htmlspecialchars($contactsArray[$i]->getName())?>
									</strong>
									<em>
                    <?=htmlspecialchars($contactsArray[$i]->getRelation())?>
                  </em>
								</p>
								<p class="mb-0">
									<a href="tel:<?=htmlspecialchars($contactsArray[$i]->getRFCContactNumber())?>">
										<?=htmlspecialchars($contactsArray[$i]->getNationalContactNumber())?>
									</a>
								</p>
							</div>
							<div class="col text-sm-right">
								<a href="<?=autoUrl("renewal/emergencycontacts/edit/" .
								$contactsArray[$i]->getID())?>" class="btn btn-primary">
									Edit
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php
			} ?>
			</div>

			<p class="">
				<a href="<?= autoUrl("renewal/emergencycontacts/new") ?>" class="btn btn-secondary">Add a New Contact</a>
			</p>

			<p>Please inform people if you have added them as an emergency contact.</p>

			<?php if (sizeof($contactsArray) > 0) { ?>
			<p>
				Ready to move on?
			</p>
			<p>
				<button type="submit" class="btn btn-success">Save and Continue</button>
			</p>
			<?php } else { ?>
			<p>You must have at least two emergency contacts available (including yourself).</p>
			<?php } ?>
		</form>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
