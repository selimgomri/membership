<?php

global $db;

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50&nbsp;Free','100&nbsp;Free','200&nbsp;Free','400&nbsp;Free','800&nbsp;Free','1500&nbsp;Free','50&nbsp;Back','100&nbsp;Back','200&nbsp;Back','50&nbsp;Breast','100&nbsp;Breast','200&nbsp;Breast','50&nbsp;Fly','100&nbsp;Fly','200&nbsp;Fly','100&nbsp;IM','150&nbsp;IM','200&nbsp;IM','400&nbsp;IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','150IMTime','200IMTime','400IMTime',];

$sql = $db->prepare("SELECT * FROM ((`galaEntries` INNER JOIN `members` ON
`members`.`MemberID` = `galaEntries`.`MemberID`) INNER JOIN `galas` ON
galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ?;");
$sql->execute([$id]);
$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
	halt(404);
}

if ($_SESSION['AccessLevel'] == 'Parent' && $row['UserID'] != $_SESSION['UserID']) {
	halt(404);
}

$member = $row['MemberID'];

$type = null;
if ($row['CourseLength'] == "SHORT") {
	$type = "SCPB";
} else {
	$type = "LCPB";
}
$getTimes = $db->prepare("SELECT * FROM `times` WHERE `MemberID` = ? AND `Type` = ?;");
$getTimes->execute([
	$member,
	$type
]);
$times = $getTimes->fetch(PDO::FETCH_ASSOC);

try {
	$db->beginTransaction();

	for ($i = 0; $i < sizeof($swimsArray); $i++) {
		$time = "";
		if (isset($_POST[$swimsTimeArray[$i] . "Mins"]) && $_POST[$swimsTimeArray[$i] . "Mins"] != "") {
			$time .= $_POST[$swimsTimeArray[$i] . "Mins"] . ':';
		} else {
			$time .= '0:';
		}
		if (isset($_POST[$swimsTimeArray[$i] . "Secs"]) && $_POST[$swimsTimeArray[$i] . "Secs"] != "") {
			$time .= str_pad($_POST[$swimsTimeArray[$i] . "Secs"], 2, "0", STR_PAD_LEFT) . '.';
		} else {
			$time .= '00.';
		}
		if (isset($_POST[$swimsTimeArray[$i] . "Hunds"]) && $_POST[$swimsTimeArray[$i] . "Hunds"] != "") {
			$time .= str_pad($_POST[$swimsTimeArray[$i] . "Hunds"], 2, "0", STR_PAD_LEFT);
		} else {
			$time .= '00';
		}

		if ($time == '0:00.00') {
			$time = null;
		}
		// Target string must be trusted
		$target = $swimsTimeArray[$i];
		$sql = $db->prepare("UPDATE `galaEntries` SET `$target` = ? WHERE `EntryID` = ?;");
		$sql->execute([$time, $id]);
	}
	$db->commit();
	$_SESSION['UpdateSuccess'] = true;
} catch (Exception $e) {
	$db->rollBack();
	$_SESSION['UpdateSuccess'] = false;
}

header("Location: " . currentUrl());
