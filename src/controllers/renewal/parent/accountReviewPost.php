<?php

global $db;

$status = true;
$statusMessage = "";

$forename = $surname = $email = $emailComms = $sms = $smsComms = "";

if ($_POST['forename'] != "") {
	$forename = $_POST['forename'];
} else {
	$status = false;
	$statusMessage .= "<li>No Forename Supplied</li>";
}

if ($_POST['surname'] != "") {
	$surname = $_POST['surname'];
} else {
	$status = false;
	$statusMessage .= "<li>No Surname Supplied</li>";
}

if (isset($_POST['emailContactOK']) && $_POST['emailContactOK'] == 1) {
	$emailComms = 1;
} else {
	$emailComms = 0;
}

if ($_POST['mobile'] != "") {
	$sms = $_POST['mobile'];
} else {
	$status = false;
	$statusMessage .= "<li>No Phone Number Provided</li>";
}

if (isset($_POST['smsContactOK']) && $_POST['smsContactOK'] == 1) {
	$smsComms = 1;
} else {
	$smsComms = 0;
}

if ($status) {
  try {
    $updateUser = $db->prepare("UPDATE `users` SET `Forename` = ?, `Surname` = ?, `EmailComms` = ?, `Mobile` = ?, `MobileComms` = ? WHERE `UserID` = ?");
    $updateUser->execute([
      $forename,
      $surname,
      $emailComms,
      $sms,
      $smsComms,
      $_SESSION['UserID']
    ]);

		// Success move on
		$updateRenewal = $db->prepare("UPDATE `renewalProgress` SET `Substage` = `Substage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?");
    $updateRenewal->execute([
      $renewal,
      $_SESSION['UserID']
    ]);
		header("Location: " . autoUrl("renewal/go"));
	} catch (Exception $e) {
		reportError($e);
		$status = false;
		$statusMessage .= "<li>Database Error - Contact support</li>";
	}
}

if (!$status) {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>An error occured when we tried to update our records</strong>
	<ul class=\"mb-0\">" . $statusMessage . "</ul></div>";
	header("Location: " . currentUrl());
}
