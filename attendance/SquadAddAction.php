<?php
$squadNameUpdate = $squadFeeUpdate = $squadCoachUpdate = $squadTimetableUpdate = $squadCoCUpdate = "";
$sql = "SELECT * FROM `squads` WHERE squads.SquadID = '$id';";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$squadName = $row['SquadName'];
$squadFee= $row['SquadFee'];
$squadCoach = $row['SquadCoach'];
$squadTimetable = $row['SquadTimetable'];
$squadCoC = $row['SquadCoC'];
$squadDeleteKey = $row['SquadKey'];

if (isset($_POST['squadName'])) {
  $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squadName']))));
  if ($postContent != $squadName) {
    $sql = "UPDATE `squads` SET `SquadName` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadNameUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadFee'])) {
  $postContent = mysqli_real_escape_string($link, number_format(trim(htmlspecialchars(ucwords($_POST['squadFee']))),2,'.',''));
  if ($postContent != $squadFee) {
    $sql = "UPDATE `squads` SET `SquadFee` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadFeeUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadCoach'])) {
  $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squadCoach']))));
  if ($postContent != $squadCoach) {
    $sql = "UPDATE `squads` SET `SquadCoach` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadCoachUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadTimetable'])) {
  $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(strtolower($_POST['squadTimetable']))));
  if ($postContent != $squadTimetable) {
    $sql = "UPDATE `squads` SET `SquadTimetable` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadTimetableUpdate = true;
    $update = true;
  }
}
if (isset($_POST['squadCoC'])) {
  $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(lcfirst($_POST['squadCoC']))));
  if ($postContent != $squadCoC) {
    $sql = "UPDATE `squads` SET `SquadCoC` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadCoCUpdate = true;
    $update = true;
  }
}

?>
