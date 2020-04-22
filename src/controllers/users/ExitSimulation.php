<?php

$db = app()->db;

$target = $_SESSION['UserID'];

try {
	$query = $db->prepare("SELECT * FROM `users` WHERE `UserID` = ?");
	$query->execute([$_SESSION['UserSimulation']['RealUser']]);

	$info = $query->fetch(PDO::FETCH_ASSOC);

	if ($info == null) {
		halt(404);
	}

	$_SESSION['UserSimulation'] = null;
	$_SESSION['UserSimulation'] = [];
	unset($_SESSION['UserSimulation']);

	$_SESSION = [];

	session_destroy();

	$_SESSION['Username'] = 		$info['Username'];
	$_SESSION['EmailAddress'] = $info['EmailAddress'];
	$_SESSION['Forename'] = 		$info['Forename'];
	$_SESSION['Surname'] = 			$info['Surname'];
	$_SESSION['UserID'] = 			$info['UserID'];
	$_SESSION['LoggedIn'] = 		1;

	$userObject = new \User($info['UserID'], true);

	header("Location: " . autoUrl("users/" . $target));
} catch (Exception $e) {
	reportError($e);
	header("Location: " . autoUrl(""));
}