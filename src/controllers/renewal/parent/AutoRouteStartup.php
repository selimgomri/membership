<?php

// Welcome to the Parent Renewal Autorouter
// The script takes the user's saved state and continues where left off
// Also controls single session progress.

global $db;

function renewalProgress($user) {
  global $db;
  $details = null;
	if (user_needs_registration($user)) {
		$details = $db->prepare("SELECT * FROM `renewalProgress` WHERE `RenewalID` = 0 AND `UserID` = ?");
	} else {
		$details = $db->prepare("SELECT * FROM `renewals` LEFT JOIN
		`renewalProgress` ON renewals.ID = renewalProgress.RenewalID WHERE
		`StartDate` <= CURDATE() AND CURDATE() <= `EndDate` AND `UserID` = ? ORDER
		BY renewals.ID DESC, renewalProgress.ID DESC");
	}
	$details->execute([$user]);
	return $details->fetch(PDO::FETCH_ASSOC);
}

function latestRenewal() {
	global $db;
	$latest = $db->query("SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE()
	AND CURDATE() <= `EndDate` ORDER BY renewals.ID DESC");
	return $latest;
}

function getNextSwimmer($user, $current = 0, $rr_only = false) {
	global $db;
	$sql = "SELECT `MemberID` FROM `members` WHERE `UserID` = ? AND `MemberID` > ?";
	$data = [
		$user,
		$current
	];
	if ($rr_only == true) {
		$sql = "SELECT `MemberID` FROM `members` WHERE `UserID` = ? AND `MemberID` > ? AND `RR` = 1";
	}

	try {
		$query = $db->prepare($sql);
		$query->execute($data);
	} catch (PDOException $e) {
		halt(500);
	}
	$row = $query->fetch(PDO::FETCH_ASSOC);
	$member = $row['MemberID'];

	if (!$row) {
		return false;
	}
	return $member;
}

function isPartialRegistration() {
	global $db;
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['UserID']]);
	} catch (PDOException $e) {
		halt(500);
	}
	$total_swimmers = (int) $query->fetchColumn();
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ? AND RR = ? ORDER
	BY `MemberID` ASC";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['UserID'], 1]);
	} catch (PDOException $e) {
		halt(500);
	}
	$new_swimmers = (int) $query->fetchColumn();
	if ($total_swimmers != $new_swimmers) {
		return true;
	}
	return false;
}

//$currentRenewal = renewalProgress($user);
$currentRenewalDetails = renewalProgress($user);

$renewal = null;

if ($currentRenewalDetails == null) {
	// Create a new Progress Record
	$latestRenewal = latestRenewal();
  if ($latestRenewal == null) {
    halt(404);
  }

	$row = $latestRenewal->fetch(PDO::FETCH_ASSOC);
	$renewal = $row['ID'];
	if (user_needs_registration($user)) {
		$renewal = 0;
	}
	$date = date("Y-m-d");

  $addRenewal = $db->prepare("INSERT INTO `renewalProgress` (`UserID`, `RenewalID`, `Date`, `Stage`, `Substage`, `Part`) VALUES (?, ?, ?, '0', '0', '0')");
  $addRenewal->execute([
    $_SESSION['UserID'],
    $renewal,
    $date
  ]);
} else {
	$latestRenewal = latestRenewal();
	$row = $latestRenewal->fetch(PDO::FETCH_ASSOC);
	$renewal = $row['ID'];
	if (user_needs_registration($user)) {
		$renewal = 0;
	}
}

$row = renewalProgress($user);

$renewalName = $row['Name'];

$stage = $row['Stage'];
$substage = $row['Substage'];
$part = $row['Part'];

// End of startup code
