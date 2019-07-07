<?php

global $db;
$member = null;

if (false/*isPartialRegistration()*/) {
	$member = getNextSwimmer($_SESSION['UserID'], 0, true);
} else {
	$sql = $db->prepare("SELECT * FROM `members` WHERE `UserID` = ? ORDER BY `MemberID` ASC LIMIT 1");
	$sql->execute([$_SESSION['UserID']]);

	$row = $sql->fetch(PDO::FETCH_ASSOC);

	if ($row == null) {
		halt(404);
	}

	$member = $row['MemberID'];
}

try {
	$sql = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = '0', `Part` = ? WHERE `RenewalID` = ? AND `UserID` = ?");
	$sql->execute([
		$member,
		$renewal,
		$_SESSION['UserID']
	]);
	header("Location: " . autoUrl("renewal/go"));
} catch (Exception $e) {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<p class=\"mb-0\"><strong>An error occured when we tried to update our records</strong></p>
	<p class=\"mb-0\">Please try again</p>
	</div>";
	header("Location: " . currentUrl());
}
