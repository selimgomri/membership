<?php

global $db;

$url_path = "emergency-contacts";
if ($renewal_trap) {
	$url_path = "renewal/emergencycontacts";
}

$user = $_SESSION['UserID'];

$contact = new EmergencyContact();
$contact->connect($db);

if ($_POST['name'] != null && $_POST['name'] != "" && $_POST['num'] != null && $_POST['num'] != "") {
	$contact->new($_POST['name'], $_POST['num'], $user);
	$contact->add();

	$_SESSION['AddNewSuccess'] = '
	<div class="alert alert-success">
		<p class="mb-0">
			<strong>
				Emergency Contact added successfully
			</strong>
		</p>
	</div>
	';

	if ($renewal_trap) {
		header("Location: " . autoUrl("renewal/go"));
	} else {
		header("Location: " . autoUrl($url_path));
	}

} else {
	$_SESSION['AddNewError'] = '
	<div class="alert alert-warning">
		<p class="mb-0">
			<strong>
				We were unable to add the contact
			</strong>
		</p>
	</div>
	';

	if ($renewal_trap) {
		header("Location: " . autoUrl("renewal/go"));
	} else {
		header("Location: " . autoUrl($url_path));
	}

}
