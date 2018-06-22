<?php

$errorState = false;
$errorMessage = "";

$id = mysqli_real_escape_string($link, $id);
$newSquad = mysqli_real_escape_string($link, $_POST['newSquad']);
$movingDate = mysqli_real_escape_string($link, $_POST['movingDate']);

if ($newSquad == "" || $newSquad == 0) {
	$errorState = true;
	$errorMessage .= "<li>A new squad was not supplied</li>";
}

if ($movingDate == "") {
	$errorState = true;
	$errorMessage .= "<li>A moving date was not supplied or was malformed</li>";
}

if (!$errorState) {
	$sql = "UPDATE `moves` SET `SquadID` = '$newSquad', `MovingDate` = '$movingDate' WHERE `MoveID` = '$id';";

	if (mysqli_query($link, $sql)) {
		header("Location: " . autoUrl("squads/moves"));
	} else {
		$errorState = true;
		$errorMessage .= '<li>A database error occured.</li>';
	}
}


if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	<strong>An error occured and we could not edit the squad move</strong>
	<ul class="mb-0">' . $errorMessage . '
	</ul></div>';

	header("Location: " . autoUrl("squads/moves/edit/" . $id));
}
