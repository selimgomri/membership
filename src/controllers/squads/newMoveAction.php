<?php

$id = mysqli_real_escape_string($link, $id);
$newSquad = mysqli_real_escape_string($link, $_POST['newSquad']);
$movingDate = mysqli_real_escape_string($link, $_POST['movingDate']);
$sql = "INSERT INTO `moves` (`MemberID`, `SquadID`, `MovingDate`) VALUES ('$id', '$newSquad', '$movingDate');";

if (mysqli_query($link, $sql)) {
	header("Location: " . autoUrl("squads/moves"));
} else {
	halt(500);
}
