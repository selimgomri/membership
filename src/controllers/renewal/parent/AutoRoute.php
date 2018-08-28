<?

require 'AutoRouteStartup.php';

if ($stage == 0 && $substage < 2 && isPartialRegistration()) {
	$substage = 2;
	$sql = "UPDATE `renewalProgress` SET `Substage` = 2 WHERE `RenewalID` = 0 AND
	`UserID` = ?";
	global $db;
	try {
		$db->prepare($sql)->execute([$_SESSION['UserID']]);
	} catch (PDOException $e) {
		halt(500);
	}
}

if ($stage == 0) {
	// Stage 0 - Reviews
	if ($substage == 0) {
		include 'accountReview.php';
	} else if ($substage == 1) {
		include 'swimmerReview.php';
	} else if ($substage == 2) {
		include 'feeReview.php';
	} else {
		halt(404);
	}
} else if ($stage == 1) {
	global $db;
	// Medical Reviews
	if ($substage == 0) {
		$id = $part;
		include 'medicalReview.php';
	} else {
		halt(404);
	}
} else if ($stage == 2) {
	// Emergency Contacts
	if ($substage == 0) {
		include 'emergencyContact.php';
	} else {
		halt(404);
	}
} else if ($stage == 3) {
	$full_renewal = true;
	// Code of Conduct
	if ($full_renewal && $substage == 0) {
		include 'conductForm.php';
	} else if ($substage == 1) {
		$id = $part;
		include 'conductForm.php';
	} else {
		halt(404);
	}
} else if ($stage == 4) {
	// Administration Form
	if ($substage == 0) {
		include 'adminForm.php';
	} else {
		halt(404);
	}
} else if ($stage == 5) {
	// Fees to Pay - Membership Renewal
	if ($substage == 0) {
		include 'renewalFee.php';
	} else {
		halt(404);
	}
} else if ($stage == 6) {
	// Printable Renewal Document
	if ($substage == 0) {
		include 'renewalDocument.php';
	} else {
		halt(404);
	}
} else if ($stage == 7) {
	include 'complete.php';
} else {
	halt(500);
}
