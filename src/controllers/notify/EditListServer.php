<?php

global $db;

$name = $desc = $errorMessage = null;
$errorState = false;

if ($_POST['name'] != null && $_POST['name'] != "") {
	$name =	trim($_POST['name']);
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that name</li>";
}

if ($_POST['desc'] != null && $_POST['desc'] != "") {
	$desc = trim($_POST['desc']);
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that description</li>";
}

if (!$errorState) {
  try {
    $update = $db->prepare("UPDATE `targetedLists` SET `Name` = ?, `Description` = ? WHERE `ID` = ?");
    $update->execute([$name, $desc, $id]);
    header("Location: " . autoUrl("notify/lists/" . $id));
	} catch (Exception $e) {
		$errorState = true;
		$errorMessage .= "<li>Unable to edit item in database</li>";
	}
}

if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	Something went wrong and we couldn\'t carry out that operation
	<ul class="mb-0">' . $errorMessage . '</ul></div>';
	header("Location: " . autoUrl("notify/lists/" . $id . "/edit"));
}
