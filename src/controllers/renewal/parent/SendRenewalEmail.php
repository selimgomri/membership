<?php

$db = app()->db;

$nextStage = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?;");
$nextStage->execute([
	$renewal,
	$_SESSION['UserID']
]);

header("Location: " . autoUrl("renewal/go"));