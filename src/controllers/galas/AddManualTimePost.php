<?php

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50&nbsp;Free','100&nbsp;Free','200&nbsp;Free','400&nbsp;Free','800&nbsp;Free','1500&nbsp;Free','50&nbsp;Back','100&nbsp;Back','200&nbsp;Back','50&nbsp;Breast','100&nbsp;Breast','200&nbsp;Breast','50&nbsp;Fly','100&nbsp;Fly','200&nbsp;Fly','100&nbsp;IM','150&nbsp;IM','200&nbsp;IM','400&nbsp;IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','150IMTime','200IMTime','400IMTime',];

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM ((`galaEntries` INNER JOIN `members` ON
`members`.`MemberID` = `galaEntries`.`MemberID`) INNER JOIN `galas` ON
galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$member = mysqli_real_escape_string($link, $row['MemberID']);

$type = null;
if ($row['CourseLength'] == "SHORT") {
	$type = "SCPB";
} else {
	$type = "LCPB";
}
$times = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `times`
WHERE `MemberID` = '$member' AND `Type` = '$type';"), MYSQLI_ASSOC);

$pagetitle = "Add Manual Time: " . $row['MForename'] . " " . $row['MSurname'];
include BASE_PATH . 'views/header.php';

for ($i = 0; $i < sizeof($swimsArray); $i++) {
	$time = "";
	if ($_POST[$swimsTimeArray[$i] . "Mins"] != "") {
		$time .= $_POST[$swimsTimeArray[$i] . "Mins"] . ':';
	}
	if ($_POST[$swimsTimeArray[$i] . "Secs"] != "") {
		$time .= $_POST[$swimsTimeArray[$i] . "Secs"] . '.';
	}
	if ($_POST[$swimsTimeArray[$i] . "Hunds"] != "") {
		$time .= $_POST[$swimsTimeArray[$i] . "Hunds"];
	}
	$time = mysqli_real_escape_string($link, $time);

	$target = mysqli_real_escape_string($link, $swimsTimeArray[$i]);
	$sql = "UPDATE `galaEntries` SET `$target` = '$time' WHERE `EntryID` = '$id';";
	mysqli_query($link, $sql);
}

header("Location: " . app('request')->curl);
