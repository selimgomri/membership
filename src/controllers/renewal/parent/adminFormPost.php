<?php

$db = app()->db;

$partial_reg = isPartialRegistration();

$id = [];

$status = true;
$statusMessage = "";

// Verify Data Consent
if ($_POST['data-agree'] != 1) {
	$status = false;
	$statusMessage .= "<li>You did not give your consent to our use of your
	data</li>";
}

$sql;
if ($partial_reg) {
	$sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth FROM `members` WHERE `UserID` = ? AND members.RR =
	1 ORDER BY `MForename` ASC, `MSurname` ASC;";
} else {
	$sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth FROM `members` WHERE `UserID` = ? ORDER BY
	`MForename` ASC, `MSurname` ASC;";
}
$getInfo = $db->prepare($sql);
$getInfo->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$row = $getInfo->fetch(PDO::FETCH_ASSOC);

for ($i = 0; $i < sizeof($row); $i++) {
	$id[$i] = $row['MemberID'];
	$name[$i] = $row['MForename'] . " " . $row['MSurname'];
	$age[$i] = date_diff(date_create($row['DateOfBirth']),
	date_create('today'))->y;
}

// Verify that all swimmers have agreed to Ts and Cs of Membership
for ($i = 0; $i < sizeof($row); $i++) {
	if ($_POST[htmlspecialchars($id[$i]) . '-tc-confirm'] != 1) {
		$status = false;
		$statusMessage .= "<li>" . htmlspecialchars($name[$i]) . " did not agree to
		the Terms and Conditions of Membership</li>";
	}

	if ($age[$i] < 12) {
		if ($_POST[htmlspecialchars($id[$i]) . '-pg-understanding'] != 1) {
			$status = false;
			$statusMessage .= "<li>You did not state that you had explained the Terms
			and Conditions of Membership to " . htmlspecialchars($name[$i]) . "</li>";
		}
	}
}

// Add or Update Photography Permissions
for ($i = 0; $i < sizeof($row); $i++) {
	if ($age[$i] < 18) {
		if (isset($_POST[htmlspecialchars($id[$i]) . '-photo-web']) ||
		isset($_POST[htmlspecialchars($id[$i]) . '-photo-soc']) ||
		isset($_POST[htmlspecialchars($id[$i]) . '-photo-nb']) ||
		isset($_POST[htmlspecialchars($id[$i]) . '-photo-film']) ||
		isset($_POST[htmlspecialchars($id[$i]) . '-photo-pro'])) {
	    setupPhotoPermissions($id[$i]);
	  }
	  // Web Photo Permissions
	  $photo[0] = 1;
	  if (!isset($_POST[htmlspecialchars($id[$i]) . '-photo-web']) ||
	  $_POST[htmlspecialchars($id[$i]) . '-photo-web'] != 1) {
	    $photo[0] = 0;
	  }
	  $update = $db->prepare("UPDATE `memberPhotography` SET `Website` = ? WHERE `MemberID` = ?");
    $update->execute([$photo[0], $id[$i]]);

	  // Social Media Photo Permissions
	  $photo[1] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-soc']) || $_POST[$id[$i] . '-photo-soc'] !=
	  1) {
	    $photo[1] = 0;
	  }
    $update = $db->prepare("UPDATE `memberPhotography` SET `Social` = ? WHERE `MemberID` = ?");
    $update->execute([$photo[1], $id[$i]]);

	  // Notice Board Photo Permissions
	  $photo[2] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-nb']) || $_POST[$id[$i] . '-photo-nb'] != 1) {
	    $photo[2] = 0;
	  }
    $update = $db->prepare("UPDATE `memberPhotography` SET `Noticeboard` = ? WHERE `MemberID` = ?");
    $update->execute([$photo[2], $id[$i]]);

	  // Filming in Training Permissions
	  $photo[3] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-film']) || $_POST[$id[$i] . '-photo-film'] != 1) {
	    $photo[3] = 0;
	  }
    $update = $db->prepare("UPDATE `memberPhotography` SET `FilmTraining` = ? WHERE `MemberID` = ?");
    $update->execute([$photo[3], $id[$i]]);

	  // Pro Photographer Photo Permissions
	  $photo[4] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-pro']) || $_POST[$id[$i] . '-photo-pro'] !=
	  1) {
	    $photo[4] = 0;
	  }
    $update = $db->prepare("UPDATE `memberPhotography` SET `ProPhoto` = ? WHERE `MemberID` = ?");
    $update->execute([$photo[4], $id[$i]]);
	}
}

// Verify Medical Permissions
for ($i = 0; $i < sizeof($row); $i++) {
	if ($age[$i] < 18) {
		if ($_POST[htmlspecialchars($id[$i]) . '-med'] != 1) {
			$status = false;
			$statusMessage .= "<li>You did not complete the medical declaration for " .
			$name[$i] . ". You cannot continue without doing this</li>";
		}
	}
}

if ($status) {
	// Update the database with current renewal state
  // $nextStage = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?");
  // $nextStage->execute([
  //   $renewal,
	// 	$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
	// ]);

	$nextSubstage = $db->prepare("UPDATE `renewalProgress` SET `Substage` = `Substage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?");
  $nextSubstage->execute([
    $renewal,
		$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
	]);
	header("Location: " . autoUrl("renewal/go"));
} else {
	$_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>There was a problem with the information you submitted</strong>
	<ul class=\"mb-0\">" . $statusMessage . "</ul>
	<p class=\"mb-0\">Please try again. You cannot renew your membership or
	register if you cannot agree to the terms and conditions on this
	page.</p></div>";
	header("Location: " . autoUrl("renewal/go"));
}
