<?php

$pagetitle = "Galas";
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
  <h1>Galas</h1>
  <p class="lead">Galas which are open for entries, or have closed. Galas in the past are not shown.</p>
  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h2>Generate a Gala Time Sheet</h2>
    <p class="lead border-bottom border-gray pb-2 mb-2">
      Gala Time Sheets give a list of each swimmer's entries to a gala along with their all-time personal bests and <? echo date("Y"); ?> personal bests.
    </p>
    <?
    $sql = "SELECT * FROM `galas` WHERE `GalaDate` >= CURDATE() ORDER BY `GalaDate` ASC;";
    $res = mysqli_query($link, $sql);
    if (mysqli_num_rows($res) > 0) {
      ?><ul class="list-unstyled mb-0"><?
      for ($i = 0; $i < mysqli_num_rows($res); $i++) {
        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
        ?>
        <li>
          <a href="<? echo autoUrl("galas/competitions/" . $row['GalaID'] .
          "/timesheet"); ?>" target="_blank"><? echo $row['GalaName']; ?></a>
        </li>
        <?
      }
      ?></ul><?
    }?>
  </div>

  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h2 class="border-bottom border-gray pb-2 mb-0">Galas Open for Entries</h2>
    <?php echo upcomingGalas($link, true);
    if ($access == "Parent") { ?>
	    <p class="mb-0">
        <a href="<?php echo autoUrl("galas/entergala"); ?>" class="btn btn-outline-dark">
          Enter a gala
        </a>
      </p>
    <?php }
    else { ?>
      <p class="mb-0">
        <a href="<?php echo autoUrl("galas/addgala"); ?>" class="btn btn-outline-dark">
          Add a gala
        </a>
      </p>
    <?php } ?>
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
		$sql = "SELECT `$col` FROM `galaEntries` WHERE `$col` = '1'";
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
								if ($swimsCountArray[$i] > 0) { ?>
		          		['<?php echo $swimsTextArray[$i]; ?>', <?php echo $swimsCountArray[$i]; ?>],
								<?php }
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
				  <h2>Statistics</h2>
				  <p class="lead border-bottom border-gray pb-2 mb-0">These statistics are gathered from all previous gala entries in our system</p>
  		    <div class="chart" id="piechart"></div>
  				<div class="chart" id="barchart"></div>
				</div>
	<?php } ?>
  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h2>Galas Closed for Entries</h2>
    <p class="lead border-bottom border-gray pb-2 mb-0">
      These galas have closed to entries, but are still in the future
    </p>
    <?php echo closedGalas($link, false); ?>
  </div>
</div>
<?php include BASE_PATH . "views/footer.php"; ?>
