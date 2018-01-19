<?php
$content = "<p class=\"lead\">Search entries for upcoming galas. Search by Gala or Gala and Surname.</p>";
$sql = "SELECT * FROM `galas` ORDER BY `galas`.`GalaDate` DESC LIMIT 0, 15;";
$result = mysqli_query($link, $sql);
$galaCount = mysqli_num_rows($result);
$content .= "
<form method=\"post\" action=\"entries-action\">
<div class=\"form-group row\">
<label class=\"col-sm-2\" for=\"gala\">Select a Gala</label>
<div class=\"col\">
<select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
//$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
for ($i = 0; $i < $galaCount; $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $lastDate = new DateTime($row['GalaDate']);
  $theDate = new DateTime('now');
  $lastDate = $lastDate->format('Y-m-d');
  $theDate = $theDate->format('Y-m-d');
  if ($lastDate >= $theDate) {
    $content .= "<option value=\"" . $row['GalaID'] . "\"";
    if ($galaID != null && $row['GalaID'] == $galaID) {
      $content .= " selected";
    }
    $content .= ">" . $row['GalaName'] . "</option>";
  }
}
$content .= "</select></div></div>
<div class=\"form-group row\">
  <label class=\"col-sm-2\" for=\"gala\">Enter Surname</label>
  <div class=\"col\">
<input class=\"form-control\" name=\"surname\" value=\"" . $surname . "\">
</div></div><p><button type=\"submit\" class=\"btn btn-success\">
  Filter
</button></p></form>";
}
if (isset($_POST['squad'])) {
$galaID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['squad'])));
}
if (isset($_POST['surname'])) {
$surname = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['surname'])));
}
$searchSQL = null;
if ($galaID != null && $surname != null) {
$searchSQL = "`galaEntries`.`GalaID` = '$galaID' AND `members`.`MSurname` = '$surname'";
}
if ($galaID == null && $surname != null) {
$searchSQL = "`members`.`MSurname` = '$surname'";
}
if ($galaID != null && $surname == null) {
$searchSQL = "`galaEntries`.`GalaID` = '$galaID'";
}
$content .= upcomingGalasBySearch($link, $searchSQL);

$_SESSION['AllEntriesResponse'] = $content;
headers("Location: entries");
?>
