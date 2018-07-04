<?php

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
	$sql = "INSERT INTO `targetedLists` (`Name`, `Description`) VALUES ('$name', '$desc');";
	if (!mysqli_query($link, $sql)) {
		$errorState = true;
		$errorMessage .= "<li>Unable to add to database</li>";
	} else {
		header("Location: " . autoUrl("notify/lists"));
	}
}

if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	Something went wrong and we couldn\'t carry out that operation
	<ul class="mb-0">' . $errorMessage . '</ul></div>';
	header("Location: " . autoUrl("notify/lists/new"));
}
