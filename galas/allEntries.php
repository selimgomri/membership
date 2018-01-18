<?php
$pagetitle = "Gala Entries";
$galaID = null;
$title = "View Gala Entries by Gala";
if (isset($_POST['squad'])) {
  $galaID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['squad'])));
}
$content = "<p class=\"lead\">All entries for upcoming galas.</p>";
$sql = "SELECT * FROM `galas` ORDER BY `galas`.`GalaDate` DESC LIMIT 0, 15;";
$result = mysqli_query($link, $sql);
$galaCount = mysqli_num_rows($result);
$content .= "
<form method=\"post\">
<div class=\"input-group form-group\">
<div class=\"input-group-prepend\">
  <span class=\"input-group-text\" for=\"gala\">Select a Gala</span>
</div>
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
$content .= "</select><div class=\"input-group-append\"><button type=\"submit\" class=\"btn btn-success\">Filter</button></div></div></form>";
$content .= upcomingGalasByID($link, $galaID);
?>
