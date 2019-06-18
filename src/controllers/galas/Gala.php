<?php

global $db;

$galas = $db->prepare("SELECT GalaName, ClosingDate, GalaDate, GalaVenue, CourseLength FROM galas WHERE GalaID = ?");
$galas->execute([$id]);
$gala = $galas->fetch(PDO::FETCH_ASSOC);

$numEntries = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE GalaID = ?");
$numEntries->execute([$id]);
$numEntries = $numEntries->fetchColumn();

$entries = $db->prepare("SELECT * FROM galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID WHERE GalaID = ?");
if ($_SESSION['AccessLevel'] == "Parent") {
  $entries = $db->prepare("SELECT * FROM galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID WHERE GalaID = ? AND UserID = ?");
  $entries->execute([$id, $_SESSION['UserID']]);
} else {
  $entries->execute([$id]);
}

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

$pagetitle = htmlspecialchars($gala['GalaName']) . " - Galas";
include BASE_PATH . "views/header.php";
include "galaMenu.php";
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>
        <?=htmlspecialchars($gala['GalaName'])?>
      </h1>
      <p class="lead">
        <?=htmlspecialchars($gala['GalaVenue'])?>
      </p>

    </div>
  </div>

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
      <?php } else if ($gala['CourseLength'] == "LONG") { ?>
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

    <div class="col-sm-6 col-md-4">
      <h3 class="h6">Number of entries</h3>
      <p><?=$numEntries?></p>
    </div>
  </div>

  <h2>Entries</h2>
  <p class="lead">
    There have been <?=$numEntries?> entries to this gala
  </p>
  
  <?php

  $entry = $entries->fetch(PDO::FETCH_ASSOC);

  if ($entry != null) { ?>

  <div class="row">
  <?php do { ?>
    <div class="col-md-6 col-lg-4">
      <h3>
        <?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?>
      </h3>

      <p><small class="text-muted">
        <strong>Date of Birth:</strong> <?=date('d/m/Y', strtotime($entry['DateOfBirth']))?>, <strong>Swim England:</strong> <?=htmlspecialchars($entry['ASANumber'])?>
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

<?php include BASE_PATH . "views/footer.php"; ?>
