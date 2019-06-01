<?php

$userID = $_SESSION['UserID'];
$use_white_background = true;

$pagetitle = "Galas";
include BASE_PATH . "views/header.php";
include "galaMenu.php";
?>
<div class="container">
  <h1>Galas</h1>
  <p class="lead">Your current entries and galas you can enter.</p>

  <div class="row">
    <div class="col-lg-6">
      <div class="cell">
        <h2 class="border-bottom border-gray pb-2 mb-0">Galas open for entries</h2>
        <?= upcomingGalas($link, false, $userID) ?>
    		<hr class="mt-0">
        <p class="mb-0"><a class="btn btn-primary" href="<?= autoUrl("galas/entergala"); ?>">
    			Enter a gala <i class="fa fa-check-square-o" aria-hidden="true"></i>
    		</a></p>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="cell">
        <h2 class=>Upcoming galas you've entered</h2>
        <p class="pb-2 mb-0 border-bottom border-gray">Here are all the galas that you've entered your swimmers into. If the closing data for a gala has not yet passed, then you'll be able to edit your entry.</p>
        <?php echo enteredGalas($link, $userID); ?>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="cell">
        <h2>Generate a Gala Time Sheet</h2>
        <p class="lead border-bottom border-gray pb-2 mb-2">
          Gala Time Sheets give a list of each of your swimmer's entries to a gala along with their all-time personal bests and <?php echo date("Y"); ?> personal bests.
        </p>
        <?php
    		$uid = mysqli_real_escape_string($link, $_SESSION['UserID']);
        $sql = "SELECT DISTINCT `galas`.`GalaID`, `GalaName` FROM ((`galas` INNER JOIN `galaEntries` ON `galas`.`GalaID` = `galaEntries`.`GalaID`) INNER JOIN members ON galaEntries.MemberID =
    		members.MemberID) WHERE `GalaDate` >= CURDATE() AND members.UserID = '$uid' ORDER BY `GalaDate` ASC;";
        $res = mysqli_query($link, $sql);
        if (mysqli_num_rows($res) > 0) {
          ?><ul class="list-unstyled mb-0"><?php
          for ($i = 0; $i < mysqli_num_rows($res); $i++) {
            $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
            ?>
            <li>
              <a href="<?php echo autoUrl("galas/competitions/" . $row['GalaID'] .
              "/timesheet"); ?>" target="_blank"><?php echo $row['GalaName']; ?></a>
            </li>
            <?php
          }
          ?></ul><?php
        } else {
    			?><p class="mt-3 mb-0">You have no gala entries. Therefore no Gala Time Sheets can be generated.</p><?php
    		}?>
      </div>
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
  	$sql = "SELECT `$col` FROM (galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) WHERE `UserID` = '$userID' AND `$col` = '1'";
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
              <?php for ($i=0; $i<sizeof($strokesCountArray); $i++) { ?>
  	          	['<?php echo $strokesCountTextArray[$i]; ?>', <?php echo $strokesCountArray[$i]; ?>],
  						<?php } ?>
  	        ]);

  	        var options = {
  	          title: 'My Gala Entries by Stroke',
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
  							if ($swimsCountArray[$i] > 0) { ?>
  	          		['<?php echo $swimsTextArray[$i]; ?>', <?php echo $swimsCountArray[$i]; ?>],
  							<?php }
  						} ?>
  	        ]);

  	        var options = {
  	          title: 'My Gala Entries by Event',
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
        <div class="col-lg-6">
    			<div class="cell">
      			<h2>Statistics</h2>
      			<p class="border-bottom border-gray pb-2 mb-0">These are statistics for all of your swimmers put together, based on entries over all time. Go to <a href="<?php echo autoUrl('swimmers'); ?>">My Swimmers</a> to see stats for each swimmer.</p>
      	    <div class="chart" id="piechart"></div>
      			<div class="chart" id="barchart"></div>
    			</div>
        </div>
  <?php } ?>
  </div>
</div>
<?php include BASE_PATH . "views/footer.php"; ?>
