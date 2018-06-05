<?php

$userID = $_SESSION['UserID'];
$id = mysqli_real_escape_string($link, $id);

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
  $pagetitle = $swimmersSecurityCheck['MForename'] . " " . $swimmersSecurityCheck['MSurname'];
  $sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames, members.MSurname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes, members.AccessKey FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
  $resultSwim = mysqli_query($link, $sqlSwim);
  $rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
  $title = null;
  ?>

  <?php include BASE_PATH . "views/header.php"; ?>

  <div class="container">

    <div class="d-flex align-items-center p-3 my-3 text-white bg-primary rounded box-shadow" id="dash">
      <img class="mr-3" src="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png" alt="" width="48" height="48">
      <div class="lh-100">
        <h1 class="h6 mb-0 text-white lh-100"><?php echo $rowSwim["MForename"] . " ";
        if ($rowSwim["MMiddleNames"] != "") {
           echo $rowSwim["MMiddleNames"] . " ";
        }
        echo $rowSwim["MSurname"]?></h1>
        <small>Swimmer, <?php echo $rowSwim["SquadName"]; ?> Squad</small>
      </div>
    </div>
    <div class="my-3 p-3 bg-white rounded box-shadow">
      <h2 class="border-bottom border-gray pb-2 mb-0">About <?php echo $rowSwim["MForename"]; ?></h2>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Date of Birth</strong>
          <?php echo date('j F Y', strtotime($rowSwim['DateOfBirth'])); ?>
        </p>
      </div>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">ASA Number</strong>
          <a href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=<?php echo $rowSwim["ASANumber"]; ?>" target="_blank" title="ASA Biographical Data"><?php echo $rowSwim["ASANumber"]; ?> <i class="fa fa-external-$link" aria-hidden="true"></i></a>
        </p>
      </div>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Attendance</strong>
          <?php echo getAttendanceByID($link, $id, 4); ?>% over the last 4 weeks, <?php echo getAttendanceByID($link, $id); ?>% over all time
        </p>
      </div>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Sex</strong>
          <?php echo $rowSwim["Gender"]; ?>
        </p>
      </div>
      <?php if ($rowSwim["MedicalNotes"] != "") { ?>
        <div class="media pt-3">
          <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">Medical Notes</strong>
            <?php echo $rowSwim["MedicalNotes"]; ?>
          </p>
        </div>
      <?php }
      if ($rowSwim["OtherNotes"] != "") { ?>
        <div class="media pt-3">
          <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">Other Notes</strong>
            <?php echo $rowSwim["OtherNotes"]; ?>
          </p>
        </div>
      <?php } ?>
      <span class="d-block text-right mt-3">
        <a href="edit/<?php echo $id;?>">Edit Details or add Medical Notes</a>
      </span>
    </div>

    <?php
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
    	if ($counter>0) { ?>
    	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});

        google.charts.setOnLoadCallback(drawPieChart);
  			google.charts.setOnLoadCallback(drawBarChart);

        function drawPieChart() {

          var data = google.visualization.arrayToDataTable([
            ['Stroke', 'Total Number of Entries'],
  					<?php for ($i=0; $i<sizeof($strokesCountArray); $i++) {
            	echo "['" . $strokesCountTextArray[$i] . "', " . $strokesCountArray[$i] . "],";
  					} ?>
          ]);

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
            ['Stroke', 'Total Number of Entries'],
            <?php for ($i=0; $i<sizeof($swimsArray); $i++) {
  						if ($swimsCountArray[$i] > 0) {
            		echo "['" . $swimsTextArray[$i] . "', " . $swimsCountArray[$i] . "],";
  						}
  					} ?>
          ]);

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
      <div class="my-3 p-3 bg-white rounded box-shadow">
        <h2 class="border-bottom border-gray pb-2 mb-0">Gala Statistics</h2>
  	    <div class="chart" id="piechart"></div>
  			<div class="chart" id="barchart"></div>
      </div>
    <div class="my-3 p-3 bg-white rounded box-shadow">
      <h2 class="border-bottom border-gray pb-2 mb-0">Squad Information</h2>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Squad</strong>
          <?php echo $rowSwim["SquadName"]; ?> Squad
        </p>
      </div>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Squad Fee</strong>
          &pound;<?php echo $rowSwim['SquadFee']; ?>
        </p>
      </div>
      <?php if ($rowSwim['SquadTimetable'] != "") { ?>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Squad Timetable</strong>
          <a href="<?php echo $rowSwim["SquadTimetable"]; ?>">Squad Timetable</a>
        </p>
      </div>
      <?php }
      if ($rowSwim['SquadCoC'] != "") { ?>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Squad Code of Conduct</strong>
          <a href="<?php echo $rowSwim["SquadCoC"]; ?>">Squad Code of Conduct</a>
        </p>
      </div>
      <?php } ?>

    <div class="media pt-3 mb-0">
      <p class="media-body pb-3 mb-0 lh-125">
        <strong class="d-block text-gray-dark">Squad Coach</strong>
        <?php echo $rowSwim["SquadCoach"]; ?>
      </p>
    </div>
  </div>

<?php } } ?>
  </div>
<script src="<?php echo autoUrl('js/tinymce/tinymce.min.js') ?>" async defer></script>
<script>
  tinymce.init({
    selector: '#medicalNotes',
    branding: false,
  });
</script>

<?php include BASE_PATH . "views/footer.php"; ?>
