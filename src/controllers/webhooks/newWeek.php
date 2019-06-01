<?php

// Get the date of the week beginning
$day = date('w');
$week_start = date('Y-m-d', strtotime('-'.$day.' days'));

// See if the date exists
$sql = "SELECT * FROM sessionsWeek ORDER BY WeekDateBeginning DESC LIMIT 1";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
if ($count > 0) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$latestWeekDB = $row['WeekDateBeginning'];
	date('Y-m-d', strtotime($latestWeekDB));
	if ($week_start != $latestWeekDB) {
		$sql = "INSERT INTO sessionsWeek (WeekDateBeginning) VALUES ('$week_start')";
		mysqli_query($link, $sql);
	}
}
else {
	$sql = "INSERT INTO sessionsWeek (WeekDateBeginning) VALUES ('$week_start')";
	mysqli_query($link, $sql);
}
