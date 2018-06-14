<?php

ignore_user_abort(true);
set_time_limit(0);

$sql = "SELECT * FROM `notify` INNER JOIN `users` ON notify.UserID = users.UserID WHERE `Status` = 'Queued' LIMIT 15;";
$result = mysqli_query($link, $sql);

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$to = $row['Forename'] . " " . $row['Surname'] . "<chris.heppell@chesterlestreetasc.co.uk>";
	$subject = $row['Subject'];
	$message = "<p>Dear " . $row['Forename'] . " " . $row['Surname'] . "</p>" . $row['Message'];
	notifySend("chris.heppell@chesterlestreetasc.co.uk", $subject, $message);

	$emailid = $row['EmailID'];
	$sql = "UPDATE `notify` SET `Status` = 'Sent' WHERE `EmailID` = '$emailid';";
	mysqli_query($link, $sql);
}
