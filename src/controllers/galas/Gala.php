<?php

global $db;

$galas = $db->prepare("SELECT GalaName, ClosingDate, GalaDate, GalaVenue, CourseLength, CoachEnters FROM galas WHERE GalaID = ?");
$galas->execute([$id]);
$gala = $galas->fetch(PDO::FETCH_ASSOC);

$numEntries = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE GalaID = ?");
$numEntries->execute([$id]);
$numEntries = $numEntries->fetchColumn();

$amountPaid = $amountLeftToPay = $amountRefunded = $total = 0;
if ($_SESSION['AccessLevel'] == 'Parent') {
  $amountPaidQuery = $db->prepare("SELECT SUM(FeeToPay) FROM galaEntries INNER JOIN members ON members.MemberID = galaEntries.MemberID WHERE GalaID = ? AND Charged = ? AND members.UserID = ?");
  $amountPaidQuery->execute([$id, 1, $_SESSION['UserID']]);
  $amountPaid = $amountPaidQuery->fetchColumn();
  $amountPaidQuery->execute([$id, 0, $_SESSION['UserID']]);
  $amountLeftToPay = $amountPaidQuery->fetchColumn();
  $total = $amountPaid + $amountLeftToPay;
  $amountRefunded = $db->prepare("SELECT SUM(AmountRefunded) FROM galaEntries INNER JOIN members ON members.MemberID = galaEntries.MemberID WHERE GalaID = ? AND members.UserID = ?");
  $amountRefunded->execute([$id, $_SESSION['UserID']]);
  $amountRefunded = $amountRefunded->fetchColumn();
} else {
  $amountPaidQuery = $db->prepare("SELECT SUM(FeeToPay) FROM galaEntries WHERE GalaID = ? AND Charged = ?");
  $amountPaidQuery->execute([$id, 1]);
  $amountPaid = $amountPaidQuery->fetchColumn();
  $amountPaidQuery->execute([$id, 0]);
  $amountLeftToPay = $amountPaidQuery->fetchColumn();
  $total = $amountPaid + $amountLeftToPay;
  $amountRefunded = $db->prepare("SELECT SUM(AmountRefunded) FROM galaEntries WHERE GalaID = ?");
  $amountRefunded->execute([$id]);
  $amountRefunded = $amountRefunded->fetchColumn();
}

$entries = $db->prepare("SELECT * FROM galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID WHERE GalaID = ?");
if ($_SESSION['AccessLevel'] == "Parent") {
  $entries = $db->prepare("SELECT * FROM galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID WHERE GalaID = ? AND UserID = ?");
  $entries->execute([$id, $_SESSION['UserID']]);
} else {
  $entries->execute([$id]);
}

$entry = $entries->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
  halt(404);
}

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
$countEntries = [];
foreach ($swimsArray as $col => $name) {
  $getCount = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE GalaID = ? AND `" . $col . "` = 1");
  $getCount->execute([$id]);
  $countEntries[$col]['Name'] = $name;
  $countEntries[$col]['Event'] = $col;
  $countEntries[$col]['Stroke'] = preg_replace("/[^a-zA-Z]+/", "", $col);
  $countEntries[$col]['Distance'] = preg_replace("/[^0-9]/", '', $col);
  $countEntries[$col]['Count'] = $getCount->fetchColumn();
  $strokeCounts[$countEntries[$col]['Stroke']] += $countEntries[$col]['Count'];
  $distanceCounts[$countEntries[$col]['Distance']] += $countEntries[$col]['Count'];
}

$pagetitle = htmlspecialchars($gala['GalaName']) . " - Galas";
include BASE_PATH . "views/header.php";
include "galaMenu.php";
?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($gala['GalaName'])?></li>
    </ol>
  </nav>
  <div class="row align-items-center">
    <div class="col-md-8">
      <h1>
        <?=htmlspecialchars($gala['GalaName'])?>
      </h1>
      <p class="lead">
        <?=htmlspecialchars($gala['GalaVenue'])?>
      </p>
    </div>
    <?php if ($_SESSION['AccessLevel'] == "Galas" || $_SESSION['AccessLevel'] == "Committee" || $_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Coach") { ?>
    <div class="col text-md-right">
      <p>
        <a href="<?=autoUrl("galas/" . $id . "/edit")?>" class="btn btn-dark">
          Edit
        </a>
      </p>
    </div>
    <?php } ?>
  </div>

  <?php if (isset($_SESSION['GalaAddedSuccess']) && $_SESSION['GalaAddedSuccess']) { ?>
  <div class="alert alert-success">We've successfully added this gala</div>
  <?php unset($_SESSION['GalaAddedSuccess']); } ?>

  <h2>About this gala</h2>

  <div class="row">
    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Gala Name</h3>
      <p><?=htmlspecialchars($gala['GalaName'])?></p>
    </div>

    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Venue</h3>
      <p><?=htmlspecialchars($gala['GalaVenue'])?></p>
    </div>

    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Category</h3>
      <?php if ($gala['CourseLength'] == "LONG") { ?>
      <p>This is a <strong>Long Course</strong> gala</p>
      <?php } else if ($gala['CourseLength'] == "SHORT") { ?>
      <p>This is a <strong>Short Course</strong> gala</p>
      <?php } else { ?>
      <p>This gala is neither short course or long course</p>
      <?php } ?>
    </div>

    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Closing date</h3>
      <p><?=date("j F Y", strtotime($gala['ClosingDate']))?></p>
    </div>

    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Finishes by</h3>
      <p><?=date("j F Y", strtotime($gala['GalaDate']))?></p>
    </div>

    <?php if ($gala['CoachEnters']) { ?>
    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Coach enters</h3>
      <p>Coaches make all entries for this gala</p>
    </div>
    <?php } ?>

    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Number of entries</h3>
      <p><?=$numEntries?></p>
    </div>

    <?php if ($entry != null) { ?>

    <div class="col-sm-6 col-md-4">
      <h3 class="h6"><?php if ($_SESSION['AccessLevel'] == 'Parent') { ?>Total cost of your entries<?php } else { ?>Total cost of entries<?php } ?></h3>
      <p>&pound;<?=number_format($total, 2)?></p>
    </div>

    <?php if ($_SESSION['AccessLevel'] != 'Parent' && $amountLeftToPay > 0) { ?>
    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Total left to charge</h3>
      <p>&pound;<?=number_format($amountLeftToPay, 2)?></p>
    </div>
    <?php } else if ($_SESSION['AccessLevel'] != 'Parent' && $amountRefunded > 0) { ?>
    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Total after refunds</h3>
      <p>&pound;<?=number_format($total - ($amountRefunded/100), 2)?></p>
    </div>
    <?php } ?>

    <?php if ($_SESSION['AccessLevel'] == 'Parent' && $amountRefunded > 0) { ?>
    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Total payable after refunds</h3>
      <p>&pound;<?=number_format($total - ($amountRefunded/100), 2)?></p>
    </div>
    <?php } ?>

    <div class="col-sm-6 col-md-4">
      <h3 class="h6"><?php if ($_SESSION['AccessLevel'] == 'Parent') { ?>Total refunded to you<?php } else { ?>Total refunded to parents<?php } ?></h3>
      <p>&pound;<?=number_format($amountRefunded/100, 2)?></p>
    </div>

    <?php } ?>
  </div>

  <?php if ($_SESSION['AccessLevel'] == 'Parent' && $gala['CoachEnters']) { ?>
  <h2>Entries are managed by your coach</h2>
  <p class="lead">Let them know you can enter this gala</p>
  <p>
    <a href="<?=autoUrl("galas/" . $id . "/indicate-availability")?>" class="btn btn-success">
      Indicate availability
    </a>
  </p>
  <?php } else if ($gala['CoachEnters'] && ($_SESSION['AccessLevel'] == 'Coach' || $_SESSION['AccessLevel'] == 'Galas' || $_SESSION['AccessLevel'] == 'Admin')) { ?>
  <h2>Manage sessions</h2>
  <p class="lead">Add sessions to this gala so parents can indicate availability</p>
  <p>
    <a href="<?=autoUrl("galas/" . $id . "/sessions")?>" class="btn btn-success">
      Manage sessions
    </a>
  </p>

  <h2>Manage entries</h2>
  <p class="lead">Add and edit entries for all competing swimmers</p>
  <p>
    <a href="<?=autoUrl("galas/" . $id . "/coaches-entries")?>" class="btn btn-success">
      Manage entries
    </a>
  </p>
  <?php } ?>

  <?php if ($numEntries > 0) { ?>
  <h2>Gala timesheet</h2>
  <p class="lead">Download a timesheet for this gala.</p>
  <p>Gala timesheets give a list of each swimmer's entries to a gala along with their all-time personal bests and
    <?=date("Y")?> personal bests.</p>
  <p>
    <a class="btn btn-success" href="<?=autoUrl("galas/" . $id . "/timesheet")?>">Download timesheet</a>
  </p>

  <h2>Charts</h2>
  <p class="lead">An easy overview of entry data.</p>
  <div class="row">
    <div class="col-lg-8">
      <canvas id="eventEntries" class="mb-3"></canvas>
    </div>
    <div class="col-lg-4">
      <canvas id="strokeEntries" class="mb-3"></canvas>
    </div>
  </div>
  <?php } ?>

  <h2>Entries</h2>
  <p class="lead">
    There have been <?=$numEntries?> entries to this gala
  </p>

  <?php

  if ($entry != null) { ?>

  <div class="row">
    <?php do { ?>
    <div class="col-md-6 col-lg-4">
      <h3>
        <?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?>
      </h3>

      <p><small class="text-muted">
          <strong>Date of Birth:</strong>&nbsp;<?=date('d/m/Y', strtotime($entry['DateOfBirth']))?>,
          <strong>Swim&nbsp;England:</strong>&nbsp;<?=htmlspecialchars($entry['ASANumber'])?>
        </small></p>

      <ul>
        <?php foreach ($swimsArray as $event => $name) { ?>
        <?php if ($entry[$event]) { ?>
        <li><?=$name?></li>
        <?php } ?>
        <?php } ?>
      </ul>
    </div>
    <?php } while ($entry = $entries->fetch(PDO::FETCH_ASSOC)); ?>
  </div>

  <?php } ?>

</div>

<script src="<?=autoUrl("public/js/Chart.min.js")?>"></script>
<script>
var ctx = document.getElementById('eventEntries').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'bar',

  // The data for our dataset
  data: {
    labels: [<?php
      foreach ($countEntries as $key => $event) {
        if ($event['Count'] > 0) {
          ?> <?=json_encode(html_entity_decode($event['Name']))?>, <?php
        }
      } ?>],
    datasets: [{
      label: <?=json_encode(html_entity_decode($gala['GalaName']))?>,
      data: [<?php
      foreach ($countEntries as $key => $event) {
        if ($event['Count'] > 0) {
          ?><?=$event['Count']?>, <?php
        }
      } ?>],
      backgroundColor: [<?php
      foreach ($countEntries as $key => $event) {
        if ($event['Count'] > 0) {
          if ($event['Stroke'] == 'Free') {
            ?> '#bd0000'
        <?php
          } else if ($event['Stroke'] == 'Back') {
            ?> '#bd00bd'
        <?php
          } else if ($event['Stroke'] == 'Breast') {
            ?> '#00bd00'
        <?php
          } else if ($event['Stroke'] == 'Fly') {
            ?> '#00bdbd'
        <?php
          } else if ($event['Stroke'] == 'IM') {
            ?> '#bdbdbd'
        <?php
          }
          ?>, <?php
        }
      } ?>
      ],
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
    labels: [<?php
      foreach ($strokeCounts as $stroke => $count) {
        if ($count > 0) {
          ?> <?=json_encode($stroke)?>, <?php
        }
      } ?>],
    datasets: [{
      label: <?=json_encode(html_entity_decode($gala['GalaName']))?>,
      data: [<?php
      foreach ($strokeCounts as $stroke => $count) {
        if ($count > 0) {
          ?> "<?=$count?>", <?php
        }
      } ?>],
      backgroundColor: [<?php
      foreach ($strokeCounts as $stroke => $count) {
        if ($count > 0) {
          if ($stroke == 'Free') {
            ?> '#bd0000'
        <?php
          } else if ($stroke == 'Back') {
            ?> '#bd00bd'
        <?php
          } else if ($stroke == 'Breast') {
            ?> '#00bd00'
        <?php
          } else if ($stroke == 'Fly') {
            ?> '#00bdbd'
        <?php
          } else if ($stroke == 'IM') {
            ?> '#bdbdbd'
        <?php
          }
          ?>, <?php
        }
      } ?>
      ],
    }],
  },

  // Configuration options go here
  // options: {}
});
</script>

<?php include BASE_PATH . "views/footer.php"; ?>