<?php

global $db;

$member = null;
if (isPartialRegistration()) {
  $member = getNextSwimmer($_SESSION['UserID'], $id, true);
} else {
  $member = getNextSwimmer($_SESSION['UserID'], $id);
}

if ($member == null) {
	halt(404);
}

try {
  $updateDatabase = $db->prepare("UPDATE `renewalProgress` SET `Substage` = '1',
  `Part` = ? WHERE `RenewalID` = ? AND `UserID` = ?");
  $updateDatabase->execute([
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
