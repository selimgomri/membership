<?php
$pagetitle = "Swimmers";
$squadID = null;
$title = "Swimmer Directory";
if (isset($_POST['squad'])) {
  $squadID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['squad'])));
}
$content = "<p class=\"lead\">A list of swimmers.</p>";
if ($access == "Committee" || $access == "Admin") {
  $content .= "<p><a href=\"add-member\" class=\"btn btn-success\">Add member</a> <a href=\"accesskeys\" class=\"btn btn-success\">Access Keys</a></p>";
}
$sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
$result = mysqli_query($link, $sql);
$squadCount = mysqli_num_rows($result);
$content .= "
<form method=\"post\">
<div class=\"input-group form-group\">
<div class=\"input-group-prepend\">
  <span class=\"input-group-text\" for=\"squad\">Select a Squad</span>
</div>
<select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
//$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
for ($i = 0; $i < $squadCount; $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $content .= "<option value=\"" . $row['SquadID'] . "\"";
  if ($squadID != null && $row['SquadID'] == $squadID) {
    $content .= " selected";
  }
  $content .= ">" . $row['SquadName'] . "</option>";
}
$content .= "</select><div class=\"input-group-append\"><button type=\"submit\" class=\"btn btn-success\">Filter</button></div></div></form>";
$content .= adminSwimmersTable($link, $squadID);
?>
