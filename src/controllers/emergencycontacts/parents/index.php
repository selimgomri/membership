<?

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

$sql = "SELECT * FROM `users` WHERE `UserID` = '$user';";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_array($res, MYSQLI_ASSOC);

$contacts = new EmergencyContacts($link);
$contacts->byParent($user);

$contactsArray = $contacts->getContacts();

$pagetitle = "My Emergency Contacts";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded box-shadow">
		<h1>
			My Emergency Contacts
		</h1>
		<p class="lead">
			Add, Edit or Remove Emergency Contacts
		</p>
		<p class="border-bottom border-gray pb-2 mb-0">
			We'll use these emergency contacts for all swimmers connected to your
			account if we can't reach you on your phone number. You can change your
			phone number in <a href="<? echo autoUrl("myaccount"); ?>">My Account</a>
		</p>
		<? if (isset($_SESSION['AddNewSuccess'])) {
			echo $_SESSION['AddNewSuccess'];
			unset($_SESSION['AddNewSuccess']);
		} ?>
		<div class="mb-3">
			<div class="media pt-3">
				<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
					<div class="row align-items-center	">
						<div class="col-9">
							<p class="mb-0">
								<strong class="d-block">
									<? echo $row['Forename'] . " " . $row['Surname']; ?> (From My
									Account)
								</strong>
								<a href="tel:<? echo $row['Mobile']; ?>">
									<? echo $row['Mobile']; ?>
								</a>
							</p>
						</div>
						<div class="col text-right">
							<a href="<? echo autoUrl("myaccount"); ?>" class="btn
							btn-primary">
								Edit
							</a>
						</div>
					</div>
				</div>
			</div>
		<? for ($i = 0; $i < sizeof($contactsArray); $i++) {
			?>
			<div class="media pt-3">
				<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
					<div class="row align-items-center	">
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
							<a href="<? echo autoUrl("emergencycontacts/edit/" .
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
		<p>
			<a href="<? echo autoUrl("emergencycontacts/new"); ?>" class="btn btn-success">
				Add New
			</a>
		</p>
		<p class="mb-0">
			The GDPR law does not require you to obtain the permission of the person
			whose contact details you are adding as this is essential operational
			information required to ensure the safety of your children.
		</p>
	</div>
</div>

<?

include BASE_PATH . 'views/footer.php';
