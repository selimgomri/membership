<?

require 'AutoRouteStartup.php';

if ($stage == 0) {
	// Stage 0 - Reviews
	if ($substage == 0) {
		include 'accountReviewPost.php';
	} else if ($substage == 1) {
		include 'swimmerReviewPost.php';
	} else if ($substage == 2) {
		include 'feeReviewPost.php';
	} else {
		halt(404);
	}
} else if ($stage == 1) {
	// Medical Reviews
	if ($substage == 0) {
		$id = $part;
		include 'medicalReviewPost.php';
	} else {
		halt(404);
	}
} else if ($stage == 2) {
	// Emergency Contacts
	if ($substage == 0) {
		//include 'emergencyContact.php';
	} else {
		halt(404);
	}
} else if ($stage == 3) {
	// Code of Conduct
	if ($substage == 0) {
		include 'conductForm.php';
	} else if ($substage == 1) {
		$id  = $part;
		include 'conductForm.php';
	} else {
		halt(404);
	}
} else if ($stage == 4) {
	// Administration Form
	if ($substage == 0) {
		include 'adminFormPost.php';
	} else {
		halt(404);
	}
} else if ($stage == 5) {
	// Fees to Pay - Membership Renewal
	if ($substage == 0) {
		include 'fees.php';
	} else {
		halt(404);
	}
} else {
	halt(500);
}
