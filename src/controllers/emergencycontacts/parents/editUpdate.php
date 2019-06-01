<?php

$url_path = "emergencycontacts";
if ($renewal_trap) {
	$url_path = "renewal/emergencycontacts";
}

$user = $_SESSION['UserID'];

$contact = new EmergencyContact();
$contact->connect($link);
$contact->getByContactID($id);

if ($contact->getUserID() != $user) {
	halt(404);
}

if ($_POST['name'] != null && $_POST['name'] != "") {
	$contact->setName($_POST['name']);
}

if ($_POST['num'] != null && $_POST['num'] != "") {
	$contact->setContactNumber($_POST['num']);
}

if ($renewal_trap) {
	header("Location: " . autoUrl("renewal/go"));
} else {
	header("Location: " . autoUrl($url_path));
}
