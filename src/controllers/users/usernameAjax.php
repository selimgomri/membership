<?php

$userID = mysqli_real_escape_string($link, $_POST['userID']);

$sql = "SELECT `Forename`, `Surname` FROM `users` WHERE `UserID` = '$userID' AND `AccessLevel` = 'Parent';";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	echo $row['Forename'] . " " . $row['Surname'];
} else {
	?>Not Found<?php
}
