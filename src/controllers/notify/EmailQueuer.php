<?php

$subject = mysqli_real_escape_string($link, $_POST['subject']);
$message = mysqli_real_escape_string($link, $_POST['message']);

$sql = "SELECT `SquadID` FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC;";
$result = mysqli_query($link, $sql);

$query = "";

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($query != "" && mysqli_real_escape_string($link, $_POST[$row['SquadID']]) == 1) {
		$query .= "OR";
	}
	if (mysqli_real_escape_string($link, $_POST[$row['SquadID']]) == 1) {
		$query .= " `SquadID` = '" . $row['SquadID'] . "' ";
	}
}

$sql = "SELECT DISTINCT users.UserID FROM `users` INNER JOIN `members` ON members.UserID = users.UserID WHERE " . $query . ";";
$result = mysqli_query($link, $sql);

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$userid = $row['UserID'];
	$sql = "INSERT INTO `notify` (`UserID`, `Subject`, `Message`) VALUES ('$userid', '$subject', '$message');";
	mysqli_query($link, $sql);
}

header("Location: " . autoUrl("notify"));
