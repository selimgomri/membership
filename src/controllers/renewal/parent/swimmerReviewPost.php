<?php

$db = app()->db;

try {
  $nextSection = $db->prepare("UPDATE `renewalProgress` SET `Substage` = `Substage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?");
  $nextSection->execute([$renewal, $_SESSION['UserID']]);
  header("Location: " . autoUrl("renewal/go"));
} catch (Exception $e) {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<p class=\"mb-0\"><strong>An error occured when we tried to update our records</strong></p>
	<p class=\"mb-0\">Please try again</p>
	</div>";
	header("Location: " . autoUrl("renewal/go"));
}
