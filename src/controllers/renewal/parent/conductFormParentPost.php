<?php

global $db;

$getFirstMember = $db->prepare("SELECT MemberID FROM `members` WHERE `UserID` = ? ORDER BY `MemberID` ASC
LIMIT 1");
$getFirstMember->execute([$_SESSION['UserID']]);
$member = $getFirstMember->fetchColumn();

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
  header("Location: " . currentUrl());
} catch (Exception $e) {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<p class=\"mb-0\"><strong>An error occured when we tried to update our records</strong></p>
	<p class=\"mb-0\">Please try again</p>
	</div>";
	header("Location: " . currentUrl());
}
