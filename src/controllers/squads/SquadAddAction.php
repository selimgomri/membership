<?php
$squadName = $squadFee = $squadCoach = $squadTimetable = $squadCoC = "";

if (isset($_POST['squadName'])) {
  $squadName = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squadName']))));
}
if (isset($_POST['squadFee'])) {
  $squadFee = mysqli_real_escape_string($link, number_format(trim(htmlspecialchars(ucwords($_POST['squadFee']))),2,'.',''));
}
if (isset($_POST['squadCoach'])) {
  $squadCoach = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squadCoach']))));
}
if (isset($_POST['squadTimetable'])) {
  $squadTimetable = mysqli_real_escape_string($link, trim(htmlspecialchars(strtolower($_POST['squadTimetable']))));
}
if (isset($_POST['squadCoC'])) {
  $squadCoC = mysqli_real_escape_string($link, trim(htmlspecialchars(lcfirst($_POST['squadCoC']))));
}
$squadKey = generateRandomString(8);

if ($squadName != null && $squadFee != null) {
  $sql = "INSERT INTO `squads` (SquadName, SquadFee, SquadCoach, SquadTimetable, SquadCoC, SquadKey) VALUES ('$squadName', '$squadFee', '$squadCoach', '$squadTimetable', '$squadCoC', '$squadKey');";
  $result = mysqli_query($link, $sql);
  header("Location: " . autoUrl('squads/'));
}

?>
