<?php

$db = app()->db;

if ($_POST['agree'] == 1) {
	$done_by_renewal = false;
	$nextMember = null;
	if (isPartialRegistration()) {
		$nextMember = getNextSwimmer($_SESSION['UserID'], $id, true);
	} else {
		$nextMember = getNextSwimmer($_SESSION['UserID'], $id);
	}

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

header("Location: " . autoUrl("renewal/go"));
