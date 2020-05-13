<?php

$db = app()->db;
$tenant = app()->tenant;

try {
	$query = $db->prepare("SELECT * FROM `users` WHERE Tenant = ? AND `UserID` = ? AND Active");
	$query->execute([
		$tenant->getId(),
		$id
	]);
} catch (Exception $e) {
	halt(500);
}

$info = $query->fetch(PDO::FETCH_ASSOC);

if (!$info) {
	halt(404);
}

$_SESSION['UserSimulation'] = [
	'RealUser'    => $_SESSION['UserID'],
	'SimUser'     => $info['UserID'],
	'SimUserName' => $info['Forename'] . ' ' . $info['Surname']
];

$_SESSION['Username'] =     $info['Username'];
$_SESSION['EmailAddress'] = $info['EmailAddress'];
$_SESSION['Forename'] =     $info['Forename'];
$_SESSION['Surname'] =      $info['Surname'];
$_SESSION['UserID'] =       $info['UserID'];
$_SESSION['LoggedIn'] =     1;

$userObject = new \User($id, true);

header("Location: " . autoUrl(""));
