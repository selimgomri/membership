<?php

// Welcome to the Parent Renewal Autorouter
// The script takes the user's saved state and continues where left off
// Also controls single session progress.

// Work out user ID
$user = null;
if (isset(app()->user)) {
	$user = app()->user->getId();
} else if (isset($_SESSION['OnboardingSessionId'])) {
	$session = \SCDS\Onboarding\Session::retrieve($_SESSION['OnboardingSessionId']);
	$user = $session->user;
} else {
	// NO USER
	halt(404);
}

$db = app()->db;
$tenant = app()->tenant;

function renewalProgress($user)
{
	$db = app()->db;
	$date = new DateTime('now', new DateTimeZone('Europe/London'));

	$details = null;
	if (user_needs_registration($user)) {
		$details = $db->prepare("SELECT * FROM `renewalProgress` WHERE `RenewalID` = 0 AND `UserID` = :user");
		$details->execute([
			'user' => $user,
		]);
	} else {
		$details = $db->prepare("SELECT * FROM `renewals` LEFT JOIN `renewalProgress` ON renewals.ID = renewalProgress.RenewalID WHERE `StartDate` <= :today AND `EndDate` >= :today AND `UserID` = :user ORDER BY renewals.ID DESC, renewalProgress.ID DESC");
		$details->execute([
			'user' => $user,
			'today' => $date->format("Y-m-d")
		]);
	}
	$details = $details->fetch(PDO::FETCH_ASSOC);
	return $details;
}

function latestRenewal()
{
	$db = app()->db;
	$tenant = app()->tenant;
	$date = new DateTime('now', new DateTimeZone('Europe/London'));

	$latest = $db->prepare("SELECT * FROM `renewals` WHERE `StartDate` <= :today AND `EndDate` >= :today AND Tenant = :tenant ORDER BY renewals.EndDate DESC LIMIT 1");
	$latest->execute([
		'today' => $date->format("Y-m-d"),
		'tenant' => $tenant->getId()
	]);
	$latestRenewal = $latest->fetch(PDO::FETCH_ASSOC);

	// Validate ready
	if (isset($latestRenewal['ID'])) {
		$getNum = $db->prepare("SELECT COUNT(*) FROM renewalMembers WHERE RenewalID = ?");
		$getNum->execute([
			$latestRenewal['ID'],
		]);

		if ($getNum->fetchColumn() == 0) {
			// Renewal cannot be completed at this time
			$latestRenewal = null;
		}
	} else {
		$latestRenewal = null;
	}

	return $latestRenewal;
}

function getNextSwimmer($user, $current = 0, $rr_only = false)
{
	$db = app()->db;

	if ($rr_only) {
		$query = $db->prepare("SELECT `MemberID` FROM `members` WHERE Active AND `UserID` = ? AND `MemberID` > ? AND `RR` = ?");
		$query->execute([
			$user,
			$current,
			true
		]);
		$next = $query->fetchColumn();
		return $next;
	} else {
		$query = $db->prepare("SELECT `MemberID` FROM `members` WHERE Active AND `UserID` = ? AND `MemberID` > ?");
		$query->execute([
			$user,
			$current
		]);
		$next = $query->fetchColumn();
		return $next;
	}
}

function isPartialRegistration()
{
	return isset($_SESSION['OnboardingSessionId']);
}

//$currentRenewal = renewalProgress($user);
$currentRenewalDetails = renewalProgress($user);

$renewal = null;

if ($currentRenewalDetails == null) {
	// Create a new Progress Record
	$latestRenewal = latestRenewal();

	if (user_needs_registration($user)) {
		$renewal = 0;
	} else if ($latestRenewal == null) {
		halt(404);
	} else {
		$renewal = $latestRenewal['ID'];
	}

	$date = date("Y-m-d");

	$doFull = app()->tenant->getBooleanKey('REQUIRE_FULL_RENEWAL');
	if ($renewal == 0) {
		$doFull = app()->tenant->getBooleanKey('REQUIRE_FULL_REGISTRATION');
	}
	$stage = $substage = $part = 0;

	if (!$doFull) {
		$stage = 5;
	}

	$addRenewal = $db->prepare("INSERT INTO `renewalProgress` (`UserID`, `RenewalID`, `Date`, `Stage`, `Substage`, `Part`) VALUES (?, ?, ?, ?, ?, ?)");
	$addRenewal->execute([
		$user,
		$renewal,
		$date,
		$stage,
		$substage,
		$part,
	]);
	$currentRenewalDetails = renewalProgress($user);
} else {
	$row = latestRenewal();
	$renewal = null;
	if ($row) {
		$renewal = $row['ID'];
	}
	if (user_needs_registration($user)) {
		$renewal = 0;
	} else if ($row == null) {
		halt(404);
	}
}

$renewalName = 'Renewal';
if (isset($currentRenewalDetails['Name'])) {
	$renewalName = $currentRenewalDetails['Name'];
} else if (user_needs_registration($user)) {
	$renewalName = '';
}

$stage = $currentRenewalDetails['Stage'];
$substage = $currentRenewalDetails['Substage'];
$part = $currentRenewalDetails['Part'];

// End of startup code
