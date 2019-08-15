<?php

$pagetitle = "Galas";

global $db;

$galas = $db->query("SELECT GalaID, GalaName, ClosingDate, GalaDate, GalaVenue, CourseLength FROM galas WHERE GalaDate >= CURDATE()");
$gala = $galas->fetch(PDO::FETCH_ASSOC);

// Arrays of swims used to check whever to print the name of the swim entered
// BEWARE This is in an order to ease inputting data into SportSystems, contrary to these arrays in other files
$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$strokeCounts = [
  'Free' => 0,
  'Back' => 0,
  'Breast' => 0,
  'Fly' => 0,
  'IM' => 0
];
$distanceCounts = [
  '50' => 0,
  '100' => 0,
  '150' => 0,
  '200' => 0,
  '400' => 0,
  '800' => 0,
  '1500' => 0
];
$chartColours = chartColours(5);
$countEntries = [];
$countEntriesEvents = [];
$countEntriesCount = [];
$countEntriesColours = [];
foreach ($swimsArray as $col => $name) {
  $getCount = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE `" . $col . "` = 1");
  $getCount->execute([$_SESSION['UserID']]);
  $count = $getCount->fetchColumn();
  if ($count > 0) {
    $countEntries[$col]['Name'] = $name;
    $countEntriesEvents[] = html_entity_decode($name);
    $countEntries[$col]['Event'] = $col;
    $countEntries[$col]['Stroke'] = preg_replace("/[^a-zA-Z]+/", "", $col);
    $countEntries[$col]['Distance'] = preg_replace("/[^0-9]/", '', $col);
    $countEntries[$col]['Count'] = $count;
    $countEntriesCount[] = $count;
    $strokeCounts[$countEntries[$col]['Stroke']] += $countEntries[$col]['Count'];
    $distanceCounts[$countEntries[$col]['Distance']] += $countEntries[$col]['Count'];
    if ($countEntries[$col]['Stroke'] == 'Free') {
      $countEntriesColours[] = $chartColours[0];
    } else if ($countEntries[$col]['Stroke'] == 'Back') {
      $countEntriesColours[] = $chartColours[1];
    } else if ($countEntries[$col]['Stroke'] == 'Breast') {
      $countEntriesColours[] = $chartColours[2];
    } else if ($countEntries[$col]['Stroke'] == 'Fly') {
      $countEntriesColours[] = $chartColours[3];
    } else if ($countEntries[$col]['Stroke'] == 'IM') {
      $countEntriesColours[] = $chartColours[4];
    }
  }
}

$strokeCountsData = array_values($strokeCounts);

include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="front-page" style="margin-bottom: -1rem;">
  <div class="container">
    <h1>Galas</h1>
    <p class="lead">Gala Entry Management at <?=htmlspecialchars(env('CLUB_NAME'))?></p>

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

  	<?php if (sizeof($countEntries) > 0) { ?>
  				<div class="mb-4">
  				  <h2>Statistics</h2>
  				  <p class="lead">These statistics are gathered from all previous gala entries in our system</p>
    		    <div class="row">
							<div class="col-lg-8">
								<canvas id="eventEntries" class="mb-3"></canvas>
							</div>
							<div class="col-lg-4">
								<canvas id="strokeEntries" class="mb-3"></canvas>
							</div>
						</div>
  				</div>
  	<?php } ?>
  </div>
</div>

<?php if (sizeof($countEntries) > 0) { ?>
<script src="<?=autoUrl("public/js/Chart.min.js")?>"></script>
<script>
var ctx = document.getElementById('eventEntries').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'bar',

  // The data for our dataset
  data: {
    labels: <?=json_encode($countEntriesEvents)?>,
    datasets: [{
      label: <?=json_encode('All entries')?>,
      data: <?=json_encode($countEntriesCount)?>,
      backgroundColor: <?=json_encode($countEntriesColours)?>,
    }],
  },

  // Configuration options go here
  options: {
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
          precision: 0,
        }
      }]
    }
  }
});
</script>

<script>
var ctx = document.getElementById('strokeEntries').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'pie',

  // The data for our dataset
  data: {
    labels: <?=json_encode(['Free', 'Back', 'Breast', 'Fly', 'IM'])?>,
    datasets: [{
      label: <?=json_encode(html_entity_decode($gala['GalaName']))?>,
      data: <?=json_encode($strokeCountsData)?>,
      backgroundColor: <?=json_encode($chartColours)?>,
    }],
  },

  // Configuration options go here
  // options: {}
});
</script>
<?php } ?>

<?php include BASE_PATH . "views/footer.php"; ?>
