<?php

$pagetitle = "Galas";

global $db;

$galas = $db->query("SELECT GalaID, GalaName, ClosingDate, GalaDate, GalaVenue, CourseLength FROM galas WHERE GalaDate >= CURDATE()");
$gala = $galas->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="front-page" style="margin-bottom: -1rem;">
  <div class="container">
    <h1>Galas</h1>
    <p class="lead">Gala Entry Management at <?=CLUB_NAME?></p>

      <h2 class="mb-4">
        Upcoming Galas
      </h2>

      <?php if ($gala) { ?>
      <div class="news-grid mb-4">
        <?php do {
          $now = new DateTime();
          $closingDate = new DateTime($gala['ClosingDate']);
          $endDate = new DateTime($gala['GalaDate']);

          ?>
          <a href="<?=autoUrl("galas/" . $gala['GalaID'])?>">
            <div>
              <span class="title mb-0 justify-content-between align-items-start">
                <span><?=htmlspecialchars($gala['GalaName'])?></span>
                <?php if ($now <= $closingDate) {?><span class="ml-2 badge badge-primary">ENTRIES OPEN</span><?php } ?>
              </span>
              <span class="d-flex mb-3"><?=htmlspecialchars($gala['GalaVenue'])?></span>
            </div>
            <?php if ($now <= $closingDate) { ?>
            <span class="category">Entries close on <?=$closingDate->format('j F Y')?></span>
            <?php } else { ?>
            <span class="category">Ends on <?=$endDate->format('j F Y')?></span>
            <?php } ?>
          </a>
        <?php } while ($gala = $galas->fetch(PDO::FETCH_ASSOC)); ?>
      </div>
      <?php } ?>

      <p>
        <a href="<?=autoUrl("galas/addgala")?>" class="btn btn-success">
          Add Gala
        </a>
      </p>

      <div class="row">
        <div class="col-md-5 py-4">
          <div class="panel bg-white">
            <h3 class="panel-title">
              Did you know?
            </h3>

            <p>
              We're able to automatically fetch times for HyTek galas.
            </p>
          </div>
        </div>
      </div>
    <!--
    GALA TIME SHEETS
    -->
    <div class="mb-4">
      <h2>Generate a Gala Time Sheet</h2>
      <p class="lead">
        Gala Time Sheets give a list of each swimmer's entries to a gala along with their all-time personal bests and <?=date("Y")?> personal bests.
      </p>
      <?php
      $sql = $db->query("SELECT DISTINCT `galas`.`GalaID`, `GalaName` FROM `galas` INNER JOIN `galaEntries` ON `galas`.`GalaID` = `galaEntries`.`GalaID` WHERE `GalaDate` >= CURDATE() ORDER BY `GalaDate` ASC;");
      $row = $sql->fetch(PDO::FETCH_ASSOC);
      if ($row != null) {
        ?><ul class="list-unstyled mb-0"><?php
        do { ?>
          <li>
            <a href="<?php echo autoUrl("galas/" . $row['GalaID'] .
            "/timesheet"); ?>" target="_blank"><?php echo $row['GalaName']; ?></a>
          </li>
          <?php
        } while ($row = $sql->fetch(PDO::FETCH_ASSOC));
        ?></ul><?php
      } else {
  			?><p class="mb-0">There are no galas with corresponding entries.</p><?php
  		}?>
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
  				<div class="mb-4">
  				  <h2>Statistics</h2>
  				  <p class="lead border-bottom border-gray pb-2 mb-0">These statistics are gathered from all previous gala entries in our system</p>
    		    <div class="chart" id="piechart"></div>
    				<div class="chart" id="barchart"></div>
  				</div>
  	<?php } ?>
  </div>
</div>
<?php include BASE_PATH . "views/footer.php"; ?>
