<?php
$sql = "SELECT * FROM `squads` WHERE squads.SquadID = '$id';";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$title = $pagetitle = "Add a Squad";
$content = "<form method=\"post\" action=\"addsquad-action\">";
$content .= "
<div class=\"form-group\">
  <label for=\"squadName\">Squad Name</label>
  <input type=\"text\" class=\"form-control\" id=\"squadName\" name=\"squadName\" placeholder=\"Enter Squad Name\">
</div>
<div class=\"form-group\">
  <label for=\"squadFee\" class=\"form-label\">Squad Fee</label>
  <div class=\"input-group\">
    <div class=\"input-group-prepend\">
      <span class=\"input-group-text\">&pound;</span>
    </div>
    <input type=\"text\" class=\"form-control\" id=\"squadFee\" name=\"squadFee\" aria-describedby=\"squadFeeHelp\" placeholder=\"eg 50.00\">
  </div>
  <small id=\"squadFeeHelp\" class=\"form-text text-muted\">A squad can have a fee of &pound;0.00 if it represents a group for non paying members</small>
</div>
<div class=\"form-group\">
  <label for=\"squadCoach\">Squad Coach</label>
  <input type=\"text\" class=\"form-control\" id=\"squadCoach\" name=\"squadCoach\" placeholder=\"Enter Squad Coach\">
</div>
<div class=\"form-group\">
  <label for=\"squadTimetable\">Squad Timetable</label>
  <input type=\"text\" class=\"form-control\" id=\"squadTimetable\" name=\"squadTimetable\" placeholder=\"Enter Squad Timetable Address\">
</div>
<div class=\"form-group\">
  <label for=\"squadCoC\">Squad Code of Conduct</label>
  <input type=\"text\" class=\"form-control\" id=\"squadCoC\" name=\"squadCoC\" placeholder=\"Enter Squad Code of Conduct Address\">
</div>";
$content .= "<p><button class=\"btn btn-success\" type=\"submit\">Add Squad</button></p>";
$content .= "</form>";

?>
