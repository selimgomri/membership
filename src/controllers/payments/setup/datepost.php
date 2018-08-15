<?php

$user = $_SESSION['UserID'];
// $date = mysqli_real_escape_string($link, $_POST['chosenDay']);
$date = 1;

$sql = "SELECT * FROM `paymentSchedule` WHERE `UserID` = '$user';";
$scheduleExists = mysqli_num_rows(mysqli_query($link, $sql));
if ($scheduleExists > 0) {
	header("Location: " . autoUrl("payments/setup/2"));
}

if ($date == null || $date == "") {
	header("Location: " . autoUrl("payments/setup/1"));
} else {
	$sql = "INSERT INTO `paymentSchedule` (`UserID`, `Day`) VALUES ('$user', '$date');";
	mysqli_query($link, $sql);
	header("Location: " . autoUrl("payments/setup/2"));
}
