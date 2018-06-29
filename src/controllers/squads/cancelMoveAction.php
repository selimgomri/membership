<?php

$id = mysqli_real_escape_string($link, $id);
$sql = "DELETE FROM `moves` WHERE `MoveID` = '$id';";

if (mysqli_query($link, $sql)) {
	header("Location: " . autoUrl("squads/moves"));
} else {
	halt(500);
}
