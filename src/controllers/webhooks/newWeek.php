<?php

global $db;

function insertWeekStart($week) {
	global $db;
	$insert = $db->prepare("INSERT INTO sessionsWeek (WeekDateBeginning) VALUES (?)");
	$insert->execute([$week]);
}

// Get the date of the week beginning
$day = date('w');
$week_start = date('Y-m-d', strtotime('-'.$day.' days'));

// See if the date exists
$getLatestWeek = $db->query("SELECT WeekDateBeginning FROM sessionsWeek ORDER BY WeekDateBeginning DESC LIMIT 1");
$latestWeekDB = $getLatestWeek->fetchColumn();
if ($latestWeekDB != null) {
	date('Y-m-d', strtotime($latestWeekDB));
	if ($week_start != $latestWeekDB) {
		insertWeekStart($week_start);
	}
}
else {
	insertWeekStart($week_start);
}
