<?php

$id = mysqli_real_escape_string($link, $id);

$name = $desc = $errorMessage = null;
$errorState = false;

if ($_POST['name'] != null && $_POST['name'] != "") {
	$name =	mysqli_real_escape_string($link, $_POST['name']);
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that name</li>";
}

if ($_POST['desc'] != null && $_POST['desc'] != "") {
	$desc = mysqli_real_escape_string($link, $_POST['desc']);
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that description</li>";
}

if (!$errorState) {
	$sql = "UPDATE `targetedLists` SET `Name` = '$name', `Description` = '$desc' WHERE `ID` = '$id';";
	if (!mysqli_query($link, $sql)) {
		$errorState = true;
		$errorMessage .= "<li>Unable to edit item in database</li>";
	} else {
		header("Location: " . autoUrl("notify/lists/" . $id));
	}
}

if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	Something went wrong and we couldn\'t carry out that operation
	<ul class="mb-0">' . $errorMessage . '</ul></div>';
	header("Location: " . autoUrl("notify/lists/" . $id . "/edit"));
}
