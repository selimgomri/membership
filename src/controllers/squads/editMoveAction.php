<?php

$id = mysqli_real_escape_string($link, $id);
$newSquad = mysqli_real_escape_string($link, $_POST['newSquad']);
$movingDate = mysqli_real_escape_string($link, $_POST['movingDate']);
$sql = "UPDATE `moves` SET `SquadID` = '$newSquad', `MovingDate` = '$movingDate' WHERE `MoveID` = '$id';";

if (mysqli_query($link, $sql)) {
	header("Location: " . autoUrl("squads/moves"));
} else {
	halt(500);
}
