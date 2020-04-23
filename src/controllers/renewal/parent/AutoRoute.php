<?php

require 'AutoRouteStartup.php';

/*if ($stage == 0 && $substage < 2 && isPartialRegistration()) {
	$substage = 2;
	$sql = "UPDATE `renewalProgress` SET `Substage` = 2 WHERE `RenewalID` = 0 AND
	`UserID` = ?";
	$db = app()->db;
	try {
		$db->prepare($sql)->execute([$_SESSION['UserID']]);
	} catch (PDOException $e) {
		halt(500);
	}
}*/

if ($stage == 0) {
	// Stage 0 - Reviews
	if ($substage == 0) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/account-review"));
		} else {
			include 'accountReview.php';
		}
	} else if ($substage == 1) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/swimmer-review"));
		} else {
			include 'swimmerReview.php';
		}
	} else if ($substage == 2) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/fee-review"));
		} else {
			include 'feeReview.php';
		}
	} else if ($substage == 3) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/my-address"));
		} else {
			include 'address.php';
		}
	} else {
		halt(404);
	}
} else if ($stage == 1) {
	$db = app()->db;
	// Medical Reviews
	if ($substage == 0) {
		$id = $part;
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/medical-review/" . $id));
		} else {
			include 'medicalReview.php';
		}
	} else {
		halt(404);
	}
} else if ($stage == 2) {
	// Emergency Contacts
	if ($substage == 0) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/emergency-contacts"));
		} else {
			include 'emergencyContact.php';
		}
	} else {
		halt(404);
	}
} else if ($stage == 3) {
	$full_renewal = true;
	// Code of Conduct
	if ($full_renewal && $substage == 0) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/parent-code-of-conduct"));
		} else {
			include 'conductForm.php';
		}
	} else if ($substage == 1) {
		$id = $part;
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/swimmer-code-of-conduct/" . $id));
		} else {
			include 'conductForm.php';
		}
	} else {
		halt(404);
	}
} else if ($stage == 4) {
	// Administration Form
	if ($substage == 0) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/administration-form"));
		} else {
			include 'adminForm.php';
		}
	} else {
		halt(404);
	}
} else if ($stage == 5) {
	// Fees to Pay - Membership Renewal
	if ($substage == 0) {
		if (isset($redirect) && $redirect) {
			header("Location: " . autoUrl("renewal/go/renewal-fee"));
		} else {
			include 'renewalFee.php';
		}
	} else {
		halt(404);
	}
} else if ($stage == 6) {
	// Printable Renewal Document
	if ($substage == 0) {
		include 'SendRenewalEmail.php';
	} else {
		halt(404);
	}
} else if ($stage == 7) {
	if (isset($redirect) && $redirect) {
		header("Location: " . autoUrl("renewal/go/completed"));
	} else {
		include 'complete.php';
	}
} else {
	halt(500);
}
