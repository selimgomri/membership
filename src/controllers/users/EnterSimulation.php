<?php

global $db;

try {
	$query = $db->prepare("SELECT * FROM `users` WHERE `UserID` = ?");
	$query->execute([$id]);
} catch (Exception $e) {
	halt(500);
}

$info = $query->fetch(PDO::FETCH_ASSOC);

if (!$info) {
	halt(404);
}

$_SESSION['UserSimulation'] = [
	'RealUser' 		=> $_SESSION['UserID'],
	'SimUser'			=> $info['UserID'],
	'SimUserName'	=> $info['Forename'] . ' ' . $info['Surname']
];

$_SESSION['Username'] = 		$info['Username'];
$_SESSION['EmailAddress'] = $info['EmailAddress'];
$_SESSION['Forename'] = 		$info['Forename'];
$_SESSION['Surname'] = 			$info['Surname'];
$_SESSION['UserID'] = 			$info['UserID'];
$_SESSION['AccessLevel'] = 	$info['AccessLevel'];
$_SESSION['LoggedIn'] = 		1;

header("Location: " . autoUrl(""));
