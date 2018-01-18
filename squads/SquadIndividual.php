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
  $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squadFee']))));
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
  $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(strtolower($_POST['squadCoC']))));
  if ($postContent != $squadCoC) {
    $sql = "UPDATE `squads` SET `SquadCoC` = '$postContent' WHERE `SquadID` = '$id'";
    mysqli_query($link, $sql);
    $squadCoCUpdate = true;
    $update = true;
  }
}
if ($access == "Admin") {
  if (isset($_POST['squadDeleteDanger'])) {
    $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(strtolower($_POST['squadDeleteDanger']))));
    if ($postContent == $squadDeleteKey) {
      $sql = "DELETE FROM `squads` WHERE `SquadID` = '$id'";
      mysqli_query($link, $sql);
      header("Location: " . autoUrl("squads"));
    }
  }
}

$sql = "SELECT * FROM `squads` WHERE squads.SquadID = '$id';";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$title = $pagetitle = $row['SquadName'] . " Squad";
$content = "<form method=\"post\">";
$content .= "
<div class=\"form-group\">
  <label for=\"squadName\">Squad Name</label>
  <input type=\"text\" class=\"form-control\" id=\"squadName\" name=\"squadName\" placeholder=\"Enter Squad Name\" value=\"" . $row['SquadName'] . "\">
</div>
<div class=\"form-group\">
  <label for=\"squadFee\" class=\"form-label\">Squad Fee</label>
  <div class=\"input-group\">
    <div class=\"input-group-prepend\">
      <span class=\"input-group-text\">&pound;</span>
    </div>
    <input type=\"text\" class=\"form-control\" id=\"squadFee\" name=\"squadFee\" aria-describedby=\"squadFeeHelp\" placeholder=\"eg 50.00\" value=\"" . $row['SquadFee'] . "\">
  </div>
  <small id=\"squadFeeHelp\" class=\"form-text text-muted\">A squad can have a fee of &pound;0.00 if it represents a group for non paying members</small>
</div>
<div class=\"form-group\">
  <label for=\"squadCoach\">Squad Coach</label>
  <input type=\"text\" class=\"form-control\" id=\"squadCoach\" name=\"squadCoach\" placeholder=\"Enter Squad Coach\" value=\"" . $row['SquadCoach'] . "\">
</div>
<div class=\"form-group\">
  <label for=\"squadTimetable\">Squad Timetable</label>
  <input type=\"text\" class=\"form-control\" id=\"squadTimetable\" name=\"squadTimetable\" placeholder=\"Enter Squad Timetable Address\" value=\"" . $row['SquadTimetable'] . "\">
</div>
<div class=\"form-group\">
  <label for=\"squadCoC\">Squad Code of Conduct</label>
  <input type=\"text\" class=\"form-control\" id=\"squadCoC\" name=\"squadCoC\" placeholder=\"Enter Squad Code of Conduct Address\" value=\"" . $row['SquadCoC'] . "\">
</div>";
if ($access == "Admin") {
$content .= "
  <div class=\"alert alert-danger\">
    <div class=\"form-group mb-0\">
      <label for=\"squadDeleteDanger\"><strong>Danger Zone</strong> <br>Delete this Squad with this Key \"" . $squadDeleteKey . "\"</label>
      <input type=\"text\" class=\"form-control\" id=\"squadDeleteDanger\" name=\"squadDeleteDanger\" aria-describedby=\"squadDeleteDangerHelp\" placeholder=\"Enter the key\" onselectstart=\"return false\" onpaste=\"return false;\" onCopy=\"return false\" onCut=\"return false\" onDrag=\"return false\" onDrop=\"return false\" autocomplete=off>
      <small id=\"squadDeleteDangerHelp\" class=\"form-text\">Enter the key in quotes above and press submit. This will delete this squad.</small>
    </div>
  </div>";
}
$content .= "<p><button class=\"btn btn-success\" type=\"submit\">Update</button></p>";
$content .= "</form>";

?>
