<?php

global $db;
setupMedicalInfo($id);

$conditions = $allergies = $medicine = "";

if ($_POST['medConDis'] == 1) {
	$conditions = trim(ucfirst($_POST['medConDisDetails']));
}

if ($_POST['allergies'] == 1) {
	$allergies = trim(ucfirst($_POST['allergiesDetails']));
}

if ($_POST['medicine'] == 1) {
	$medicine = trim(ucfirst($_POST['medicineDetails']));
}

try {
  $medUpdate = $db->prepare("UPDATE `memberMedical` SET `Conditions` = ?, `Allergies` =
  ?, `Medication` = ? WHERE `MemberID` = ?");
  $medUpdate->execute([
    $conditions,
    $allergies,
    $medicine,
    $id
  ]);

  if (false/*isPartialRegistration() && !getNextSwimmer($_SESSION['UserID'], $id, true)*/) {
		$full_renewal = false;
		$substage = 1;
		$member = getNextSwimmer($_SESSION['UserID'], 0, true);
		$sql = "UPDATE `renewalProgress` SET `Stage` = 3, `Substage` = 1, `Part` = ? WHERE `RenewalID` = 0 AND `UserID` = ?";
		global $db;
		try {
			$db->prepare($sql)->execute([$member, $_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
	} else {
    $getNextSwimmer = $db->prepare("SELECT MemberID FROM `members` WHERE
    `UserID` = ? AND `MemberID` > ? ORDER BY `MemberID` ASC LIMIT 1");
		$getNextSwimmer->execute([$_SESSION['UserID'], $id]);
    $nextSwimmer = $getNextSwimmer->fetchColumn();

		if ($nextSwimmer == null) {
      $nextSection = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = '0',
			`Part` = '0' WHERE `RenewalID` = ? AND `UserID` = ?");
      $nextSection->execute([$renewal, $_SESSION['UserID']]);
		} else {
      if (false/*isPartialRegistration()*/) {
				$nextSwimmer = getNextSwimmer($_SESSION['UserID'], $id, true);
			}
      $nextSection = $db->prepare("UPDATE `renewalProgress` SET `Part` = ?
      WHERE `RenewalID` = ? AND `UserID` = ?");
      $nextSection->execute([$nextSwimmer, $renewal, $_SESSION['UserID']]);
		}
	}
	header("Location: " . autoUrl("renewal/go"));
} catch (Exception $e) {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>An error occured when we tried to update our records</strong>
	<p class=\"mb-0\">Please try again. Your membership renewal will not be
	affected by this error.</p></div>";
	header("Location: " . currentUrl());
}
