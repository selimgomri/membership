<?php
$title = $pagetitle = "Add a Squad";
$content = "
<div class=\"my-3 p-3 bg-white rounded box-shadow\">
<h2 class=\"border-bottom border-gray pb-2\">Squad Details</h2>
<form method=\"post\" action=\"addsquad-action\">";
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
$content .= "<p class=\"mb-0\"><button class=\"btn btn-outline-dark\" type=\"submit\">Add Squad</button></p>";
$content .= "</form></div>";

?>
