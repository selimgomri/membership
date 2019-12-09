<?php

global $db;

$types = ['SC', 'LC'];

// Get count of times and add row if required
$countRow = $db->prepare("SELECT COUNT(*) FROM `times` WHERE MemberID = ? AND `Type` = ?");
$insertRow = $db->prepare("INSERT INTO `times` (MemberID, LastUpdate, `Type`) VALUES (?, ?, ?)");
foreach ($types as $type) {
  $countRow->execute([$id, $type . 'PB']);
  if ($countRow->fetchColumn() == 0) {
    $lastUpdate = (new DateTime('now', new DateTimeZone('Europe/London')))->format("Y-m-d");
    $insertRow->execute([$id, $lastUpdate, $type . 'PB']);
  }
}

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','200IM','400IM',];
$swimsTextArray = ['50&nbsp;Free','100&nbsp;Free','200&nbsp;Free','400&nbsp;Free','800&nbsp;Free','1500&nbsp;Free','50&nbsp;Back','100&nbsp;Back','200&nbsp;Back','50&nbsp;Breast','100&nbsp;Breast','200&nbsp;Breast','50&nbsp;Fly','100&nbsp;Fly','200&nbsp;Fly','100&nbsp;IM','200&nbsp;IM','400&nbsp;IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','200IMTime','400IMTime',];

try {
  $db->beginTransaction();
  foreach ($types as $type) {
    for ($i = 0; $i < sizeof($swimsArray); $i++) {
      $time = "";
      if (isset($_POST[$swimsTimeArray[$i] . $type . "Mins"]) && $_POST[$swimsTimeArray[$i] . $type . "Mins"] != "") {
        $time .= $_POST[$swimsTimeArray[$i] . $type . "Mins"] . ':';
      } else {
        $time .= '0:';
      }
      if (isset($_POST[$swimsTimeArray[$i] . $type . "Secs"]) && $_POST[$swimsTimeArray[$i] . $type . "Secs"] != "") {
        $time .= str_pad($_POST[$swimsTimeArray[$i] . $type . "Secs"], 2, "0", STR_PAD_LEFT) . '.';
      } else {
        $time .= '00.';
      }
      if (isset($_POST[$swimsTimeArray[$i] . $type . "Hunds"]) && $_POST[$swimsTimeArray[$i] . $type . "Hunds"] != "") {
        $time .= str_pad($_POST[$swimsTimeArray[$i] . $type . "Hunds"], 2, "0", STR_PAD_LEFT);
      } else {
        $time .= '00';
      }
      if ($time == '0:00.00') {
        $time = null;
      }
      // Target string must be trusted
      $target = $swimsArray[$i];
      $sql = $db->prepare("UPDATE `times` SET `$target` = ? WHERE `MemberID` = ? AND `Type` = ?;");
      $sql->execute([$time, $id, $type . 'PB']);
    }
  }
	$db->commit();
	$_SESSION['UpdateSuccess'] = true;
} catch (Exception $e) {
  $db->rollBack();
  pre($e);
  exit();
	$_SESSION['UpdateSuccess'] = false;
}

header("Location: " . autoUrl("swimmers/" . $id . "/times"));