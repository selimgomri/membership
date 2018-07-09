<?

require 'AutoRouteStartup.php';

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
