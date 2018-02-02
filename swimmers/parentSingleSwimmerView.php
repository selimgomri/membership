<?php

$query = "SELECT * FROM members WHERE MemberID = '$id' ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$forename = $row['MForename'];
$middlename = $row['MMiddleNames'];
$surname = $row['MSurname'];
$dateOfBirth = $row['DateOfBirth'];
$sex = $row['Gender'];
$medicalNotes = $row['MedicalNotes'];
$otherNotes = $row['OtherNotes'];

// Get the swimmer name
$sqlSecurityCheck = "SELECT `MForename`, `MSurname`, `UserID` FROM `members` WHERE MemberID = '$id';";
$resultSecurityCheck = mysqli_query($link, $sqlSecurityCheck);
$swimmersSecurityCheck = mysqli_fetch_array($resultSecurityCheck, MYSQLI_ASSOC);

$pagetitle;
if ($swimmersSecurityCheck['UserID'] == $userID && $resultSecurityCheck) {
  $pagetitle = "Swimmer: " . $swimmersSecurityCheck['MForename'] . " " . $swimmersSecurityCheck['MSurname'];
  $sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames, members.MSurname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes, members.AccessKey FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
  $resultSwim = mysqli_query($link, $sqlSwim);
  $rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
  $title = $swimmersSecurityCheck['MForename'] . " " . $rowSwim['MMiddleNames'] . " " . $swimmersSecurityCheck['MSurname'];
  $content = "<div class=\"row\"><div class=\"col col-md-6\"><ul>";
  // Main Info Content
  $content.= "
  <li>Date of Birth: " . $rowSwim['DateOfBirth'] . "</li>
  <li>ASA Number: " . $rowSwim['ASANumber'] . "</li>
  <li>Sex: " . $rowSwim['Gender'] . "</li>
  <li>Medical Notes: " . $rowSwim['MedicalNotes'] . "</li>
  <li>Other Notes: " . $rowSwim['MedicalNotes'] . "</li>
  </ul>";
  $content .= "</div><div class=\"col-md-6\">";
  /* Stats Section */
  $swimsCountArray = [];
  $strokesCountArray = [0, 0, 0, 0, 0];
  $strokesCountTextArray = ["Freestyle", "Breaststroke", "Butterfly", "Backstroke", "Individual Medley"];
  $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
  $strokesArray = ['0','0','0','0','0','0','1','1','1','2','2','2','3','3','3','4','4','4','4',];
  $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
  $counter = 0;
  for ($i=0; $i<sizeof($swimsArray); $i++) {
  	$col = $swimsArray[$i];
  	$sql = "SELECT `$col` FROM `galaEntries` WHERE `MemberID` = '$id' AND `$col` = '1'";
  	$result = mysqli_query($link, $sql);
  	$count = mysqli_num_rows($result);
  	$swimsCountArray[$i] = $count;
  	$strokesCountArray[$strokesArray[$i]] += $count;
  	$counter += $count;
  }
  	if ($counter>0) {
  	$content .= "<script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
  	    <script type=\"text/javascript\">
  	      google.charts.load('current', {'packages':['corechart']});

  	      google.charts.setOnLoadCallback(drawPieChart);
  				google.charts.setOnLoadCallback(drawBarChart);

  	      function drawPieChart() {

  	        var data = google.visualization.arrayToDataTable([
  	          ['Stroke', 'Total Number of Entries'],";
  						for ($i=0; $i<sizeof($strokesCountArray); $i++) {
  	          	$content .= "['" . $strokesCountTextArray[$i] . "', " . $strokesCountArray[$i] . "],";
  						}
  	        $content .= "]);

  	        var options = {
  	          title: 'Gala Entries by Stroke',
  						fontName: 'Open Sans',
  						backgroundColor: {
  							fill:'transparent'
  						},
  						chartArea: {
  							left: '0',
  							right: '0',
  						}
  	        };

  	        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

  	        chart.draw(data, options);
  	      }
  				function drawBarChart() {

  	        var data = google.visualization.arrayToDataTable([
  	          ['Stroke', 'Total Number of Entries'],";
  						for ($i=0; $i<sizeof($swimsArray); $i++) {
  							if ($swimsCountArray[$i] > 0) {
  	          		$content .= "['" . $swimsTextArray[$i] . "', " . $swimsCountArray[$i] . "],";
  							}
  						}
  	        $content .= "]);

  	        var options = {
  	          title: 'Gala Entries by Event',
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

  	        var chart = new google.visualization.ColumnChart(document.getElementById('barchart'));

  	        chart.draw(data, options);
  	      }
  	    </script>
        <div class=\"cell\">
  			<h2>Gala Statistics</h2>
  	    <div id=\"piechart\"></div>
  			<div id=\"barchart\"></div>
        </div>
  	";
  }
  $content .= "<div class=\"cell\"><h2>Squad Information</h2><ul class=\"mb-0\"><li>Squad: " . $rowSwim['SquadName'] . "</li><li>Monthly Fee: &pound;" . $rowSwim['SquadFee'] . "</li>";
  if ($rowSwim['SquadTimetable'] != "") {
    $content .= "<li><a href=\"" . $rowSwim['SquadTimetable'] . "\">Squad Timetable</a></li>";
  }
  if ($rowSwim['SquadCoC'] != "") {
    $content .= "<li><a href=\"" . $rowSwim['SquadCoC'] . "\">Squad Code of Conduct</a></li>";
  }
  $content .= "</ul></div>";
  $content .= "</div></div>";

}
else {
  // Not allowed or not found
  $pagetitle = "Error 404 - Not found";
  $title = "Error 404 - Not found";
}

include "../header.php";
?>
<script src="<?php echo autoUrl('js/tinymce/tinymce.min.js') ?>" async defer></script>
<script>
  tinymce.init({
    selector: '#medicalNotes',
    branding: false,
  });
</script>
<?php

?>
