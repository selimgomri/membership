<?php

global $db;

if ($_POST['agree'] == 1) {
	$done_by_renewal = false;
if (false/*isPartialRegistration() && !getNextSwimmer($_SESSION['UserID'], $id, true)*/) {
		$full_renewal = false;
		$substage = 1;
		$member = getNextSwimmer($_SESSION['UserID'], 0, true);
		$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = 0, `Part` = 0 WHERE `RenewalID` = 0 AND `UserID` = ?";
		try {
			$db->prepare($sql)->execute([$_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
	} else if (false/*isPartialRegistration()*/) {
		$member = getNextSwimmer($_SESSION['UserID'], $id, true);
		$sql = "UPDATE `renewalProgress` SET `Part` = ? WHERE `RenewalID` = 0 AND
		`UserID` = ?";
		try {
			$db->prepare($sql)->execute([$id, $_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
	} else {
    $getNextMember = $db->prepare("SELECT MemeberID FROM `members` WHERE `UserID` = ? AND `MemberID` > ? ORDER BY `MemberID` ASC
		LIMIT 1");
    $getNextMember->execute([
      $_SESSION['UserID'],
      $id
    ]);

    $nextMember = $getNextMember->fetchColumn();

		if ($nextMember == null) {
      $nextSection = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = '0',
			`Part` = '0' WHERE `RenewalID` = ? AND `UserID` = ?");
      $nextSection->execute([
        $renewal,
        $_SESSION['UserID']
      ]);
		} else {
      $nextSection = $db->prepare("UPDATE `renewalProgress` SET Part = ? WHERE `RenewalID` = ? AND `UserID` = ?");
      $nextSection->execute([
        $nextMember,
        $renewal,
        $_SESSION['UserID']
      ]);
		}
	}
} else {
	$_SESSION['RenewalErrorInfo'] = '
	<div class="alert alert-danger">
		<p class="mb-0">
			<strong>
				You must tick to confirm you agree to this code of conduct
			</strong>
		</p>
	</div>';
}

header("Location: " . currentUrl());
