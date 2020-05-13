<?php

$db = app()->db;

use Respect\Validation\Validator as v;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

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
	$sms = null;
	try {
		$number = PhoneNumber::parse($_POST['mobile'], 'GB');
		$sms = $number->format(PhoneNumberFormat::E164);
	} catch (PhoneNumberParseException $e) {
		// 'The string supplied is too short to be a phone number.'
		$status = false;
		$statusMessage .= "
		<li>That phone number is not valid</li>
		";
	}
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
      $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
    ]);

		// Success move on
		$updateRenewal = $db->prepare("UPDATE `renewalProgress` SET `Substage` = `Substage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?");
    $updateRenewal->execute([
      $renewal,
      $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
    ]);
		header("Location: " . autoUrl("renewal/go"));
	} catch (Exception $e) {
		reportError($e);
		$status = false;
		$statusMessage .= "<li>Database Error - Contact support</li>";
	}
}

if (!$status) {
	$_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>An error occured when we tried to update our records</strong>
	<ul class=\"mb-0\">" . $statusMessage . "</ul></div>";
	header("Location: " . autoUrl("renewal/go"));
}
