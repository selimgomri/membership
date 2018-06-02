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
if ($access == "Admin") {
  if (isset($_POST['squadDeleteDanger'])) {
    $postContent = mysqli_real_escape_string($link, trim(htmlspecialchars(lcfirst($_POST['squadDeleteDanger']))));
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
if ($access == "Admin") {
$content .= "
<div class=\"row\"><div class=\"col-md-6\">
<div class=\"my-3 p-3 bg-white rounded box-shadow\">
<h2>Details</h2>
<p class=\"lead border-bottom border-gray pb-2\">View or edit the squad details</p>
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
</div>
<div class=\"alert alert-danger\">
  <div class=\"form-group mb-0\">
    <label for=\"squadDeleteDanger\"><strong>Danger Zone</strong> <br>Delete this Squad with this Key \"" . $squadDeleteKey . "\"</label>
    <input type=\"text\" class=\"form-control\" id=\"squadDeleteDanger\" name=\"squadDeleteDanger\" aria-describedby=\"squadDeleteDangerHelp\" placeholder=\"Enter the key\" onselectstart=\"return false\" onpaste=\"return false;\" onCopy=\"return false\" onCut=\"return false\" onDrag=\"return false\" onDrop=\"return false\" autocomplete=off>
    <small id=\"squadDeleteDangerHelp\" class=\"form-text\">Enter the key in quotes above and press submit. This will delete this squad.</small>
  </div>
</div>
<p><button class=\"btn btn-outline-dark\" type=\"submit\">Update</button></p></form></div></div>

<div class=\"col-md-6\">";

$sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Male';";
$result = mysqli_query($link, $sql);
$male = mysqli_num_rows($result);
$sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Female';";
$result = mysqli_query($link, $sql);
$female = mysqli_num_rows($result);

  if ($male+$female>0) {
  $content .= "<script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
      <script type=\"text/javascript\">
        google.charts.load('current', {'packages':['corechart']});

        google.charts.setOnLoadCallback(drawPieChart);
        function drawPieChart() {

          var data = google.visualization.arrayToDataTable([
            ['Gender', 'Number of Members'],
            ['Male', " . $male . "],
            ['Female', " . $female . "],
          ]);

          var options = {
            title: 'Squad Gender Split',
            fontName: 'Open Sans',
            backgroundColor: {
              fill:'transparent'
            },
            chartArea: {
              left: '0',
              right: '0',
            },
            backgroundColor: {
              fill:'transparent'
            },
            legend: {
              position: 'none',
            }
          };

          var chart = new google.visualization.PieChart(document.getElementById('piechart'));

          chart.draw(data, options);
        }
      </script>
      <div class=\"my-3 p-3 bg-white rounded box-shadow\">
      <h2>Statistics</h2>
      <p class=\"lead border-bottom border-gray pb-2 mb-0\">These statistics are gathered from our system</p>
      <div class=\"chart\" id=\"piechart\"></div>
      </div></div>
  ";

  }
  $content .= "</div></div>";
}
else {
  $content .= "<div class=\"row\"><div class=\"col-md-6\">
  <div class=\"my-3 p-3 bg-white rounded box-shadow\">
  <h2 class=\"border-bottom border-gray pb-2\">Squad Details</h2>
  <ul class=\"mb-0\">";
  if ($row['SquadFee'] > 0) {
    $content .= "<li>Squad Fee: &pound;" . $row['SquadFee'] . "</li>";
  }
  else {
    $content .= "<li>There is no fee for this squad</li>";
  }
  $content .= "
    <li>Squad Coach: " . $row['SquadCoach'] . "</li>
    <li><a href=\"" . $row['SquadTimetable'] . "\">Squad Timetable</a></li>
    <li><a href=\"" . $row['SquadCoC'] . "\">Squad Code of Conduct</a></li>
  </ul></div></div>";
  $sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Male';";
  $result = mysqli_query($link, $sql);
  $male = mysqli_num_rows($result);
  $sql = "SELECT `Gender` FROM `members` WHERE `SquadID` = '$id' AND `Gender` = 'Female';";
  $result = mysqli_query($link, $sql);
  $female = mysqli_num_rows($result);

    if ($male+$female>0) {
    $content .= "<script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
        <script type=\"text/javascript\">
          google.charts.load('current', {'packages':['corechart']});

          google.charts.setOnLoadCallback(drawPieChart);
          function drawPieChart() {

            var data = google.visualization.arrayToDataTable([
              ['Gender', 'Number of Members'],
              ['Male', " . $male . "],
              ['Female', " . $female . "],
            ]);

            var options = {
              title: 'Squad Gender Split',
              fontName: 'Open Sans',
              backgroundColor: {
                fill:'transparent'
              },
              chartArea: {
                left: '0',
                right: '0',
              },
              backgroundColor: {
                fill:'transparent'
              },
              legend: {
                position: 'none',
              }
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
          }
        </script>
        <div class=\"col-md-6\">
        <div class=\"my-3 p-3 bg-white rounded box-shadow\">
        <h2>Statistics</h2>
        <p class=\"lead border-bottom border-gray pb-2 mb-0\">These statistics are gathered from our system</p>
        <div class=\"chart\" id=\"piechart\"></div>
        </div></div></div>
    ";
  }
}

?>