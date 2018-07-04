<?php

$subject = mysqli_real_escape_string($link, $_POST['subject']);
$message = mysqli_real_escape_string($link, $_POST['message']);

$sql = "SELECT `SquadID` FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC;";
$result = mysqli_query($link, $sql);

$sql = "SELECT * FROM `targetedLists` ORDER BY `Name` ASC;";
$lists = mysqli_query($link, $sql);

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

for ($i = 0; $i < mysqli_num_rows($lists); $i++) {
	$row = mysqli_fetch_array($lists, MYSQLI_ASSOC);
	if ($query != "" && mysqli_real_escape_string($link, $_POST["TL-" . $row['ID']]) == 1) {
		$query .= "OR";
	}
	if (mysqli_real_escape_string($link, $_POST["TL-" . $row['ID']]) == 1) {
		$id = "TL-" . $row['ID'];
		$id = mysqli_real_escape_string($link, substr_replace($id, '', 0, 3));
		$query .= " `ListID` = '" . $row['ID'] . "' ";
	}
}

$sql = "SELECT DISTINCT users.UserID FROM ((`users` INNER JOIN `members` ON members.UserID = users.UserID) INNER JOIN `targetedListMembers` ON targetedListMembers.ReferenceID = members.MemberID) WHERE " . $query . ";";
$result = mysqli_query($link, $sql);

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$userid = $row['UserID'];
	$sql = "INSERT INTO `notify` (`UserID`, `Subject`, `Message`, `Status`) VALUES ('$userid', '$subject', '$message', 'Queued');";
	mysqli_query($link, $sql);
}

header("Location: " . autoUrl("notify"));
