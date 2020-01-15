<?php

global $db;

$swimmer = $db->prepare("SELECT MForename, MSurname, UserID FROM members WHERE MemberID = ?");
$swimmer->execute([$id]);
$swimmer = $swimmer->fetch(PDO::FETCH_ASSOC);

if ($swimmer == null) {
  halt(404);
}

if ($_SESSION['AccessLevel'] == 'Parent' && $swimmer['UserID'] !== $_SESSION['UserID']) {
	halt(404);
}

if (!isset($_GET['stroke']) || !isset($_GET['distance']) || !isset($_GET['course'])) {
  halt(404);
}

$order = 'time';
if (isset($_GET['order']) && $_GET['order'] == 'date') {
  $order = 'date';
}

$strokes = [
  1 => 'Free',
  2 => 'Back',
  3 => 'Breast',
  4 => 'Butterfly',
  5 => 'IM'
];

$distances = [
  1 => [
    50   => ['L' => true, 'S' => true],
    100  => ['L' => true, 'S' => true],
    200  => ['L' => true, 'S' => true],
    400  => ['L' => true, 'S' => true],
    800  => ['L' => true, 'S' => true],
    1500 => ['L' => true, 'S' => true],
  ],
  2 => [
    50  => ['L' => true, 'S' => true],
    100 => ['L' => true, 'S' => true],
    200 => ['L' => true, 'S' => true],
  ],
  3 => [
    50  => ['L' => true, 'S' => true],
    100 => ['L' => true, 'S' => true],
    200 => ['L' => true, 'S' => true],
  ],
  4 => [
    50  => ['L' => true, 'S' => true],
    100 => ['L' => true, 'S' => true],
    200 => ['L' => true, 'S' => true],
  ],
  5 => [
    100 => ['L' => false, 'S' => true],
    200 => ['L' => true, 'S' => true],
    400 => ['L' => true, 'S' => true],
  ],
];

if (!isset($distances[(int) $_GET['stroke']][(int) $_GET['distance']][$_GET['course']]) || !bool($distances[(int) $_GET['stroke']][(int) $_GET['distance']][$_GET['course']])) {
  halt(404);
}

$getTime = null;
if ($order == 'time') {
  $getTime = $db->prepare("SELECT `Time`, `Date`, `Round`, `Stroke`, `Distance`, `Name`, `City`, `GalaName` FROM ((meetResults INNER JOIN meetsWithResults ON meetResults.Meet = meetsWithResults.Meet) LEFT JOIN galas ON meetsWithResults.Gala = galas.GalaID) WHERE Member = ? AND Stroke = ? AND Distance = ? AND meetResults.Course = ? ORDER BY IntTime ASC");
} else {
  $getTime = $db->prepare("SELECT `Time`, `Date`, `Round`, `Stroke`, `Distance`, `Name`, `City`, `GalaName` FROM ((meetResults INNER JOIN meetsWithResults ON meetResults.Meet = meetsWithResults.Meet) LEFT JOIN galas ON meetsWithResults.Gala = galas.GalaID) WHERE Member = ? AND Stroke = ? AND Distance = ? AND meetResults.Course = ? ORDER BY `Date` DESC");
}
$getTime->execute([$id, (int) $_GET['stroke'], (int) $_GET['distance'], $_GET['course']]);
$result = $getTime->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($swimmer['MForename'] . ' ' . $swimmer['MSurname']) . ' Times';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("members")?>">Members</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("members/" . $id)?>"><?=htmlspecialchars(mb_substr($swimmer["MForename"], 0, 1, 'utf-8'))?><?=htmlspecialchars(mb_substr($swimmer["MSurname"], 0, 1, 'utf-8'))?></a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("members/" . $id . "/times")?>">Best times</a></li>
			<li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars((int) $_GET['distance'])?>m <?=$strokes[(int) $_GET['stroke']]?></li>
		</ol>
	</nav>

  <h1><?=htmlspecialchars((int) $_GET['distance'])?>m <?=$strokes[(int) $_GET['stroke']]?> - <?=htmlspecialchars($swimmer["MForename"])?> <?=htmlspecialchars($swimmer["MSurname"])?></h1>
  <p class="lead">Welcome to our new best times system.</p>
  <p>Our new system uses a results file for every gala so has only been populated with results going back a reasonable period of time.</p>

  <?php if ($order == 'time') { ?>
  <p>Swims in time order. <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/times/event?course=" . $_GET['course'] . "&stroke=" . $_GET['stroke'] . "&distance=" . $_GET['distance'] . "&order=date"))?>">Switch to date order</a></p>
  <?php } else { ?>
  <p>Swims in date order. <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/times/event?course=" . $_GET['course'] . "&stroke=" . $_GET['stroke'] . "&distance=" . $_GET['distance'] . "&order=time"))?>">Switch to time order</a></p>
  <?php } ?>

  <?php if ($result) { ?>
  <div class="mb-3">
    <div class="d-none d-lg-block py-1 px-2 bg-primary text-white">
      <div class="row">
        <div class="col-lg-2">
          <strong>Date</strong>
        </div>
        <div class="col-lg-2 text-right">
          <strong>Time</strong>
        </div>
        <div class="col-lg-4">
          <strong>Gala</strong>
        </div>
        <div class="col-lg-4">
          <strong>City</strong>
        </div>
      </div>
    </div>
    <?php $count = 0; ?>
    <?php do { ?>
    <div class="py-1 px-2  <?php if ($count%2 == 0) { ?>bg-light<?php } ?>">
      <div class="row">
        <div class="col-4 col-md-4 col-lg-2 text-truncate text-right text-lg-left">
          <?=htmlspecialchars((new DateTime($result['Date'], new DateTimeZone('Europe/London')))->format("j/n/Y"))?>
        </div>
        <div class="col-4 col-md-4 col-lg-2 text-right">
          <span class="mono"><?=htmlspecialchars($result['Time'])?></span>
        </div>
        <div class="col-8 col-lg-4 text-truncate" title="<?php if ($result['GalaName'] == null) { ?><?=htmlspecialchars($result['Name'])?><?php } else { ?><?=htmlspecialchars($result['GalaName'])?><?php } ?>">
          <?php if ($result['GalaName'] == null) { ?>
          <?=htmlspecialchars($result['Name'])?>
          <?php } else { ?>
          <?=htmlspecialchars($result['GalaName'])?>
          <?php } ?>
        </div>
        <div class="col-4 text-right text-lg-left col-lg-4 text-truncate">
          <?=htmlspecialchars($result['City'])?>
        </div>
      </div>
    </div>
    <?php $count++; } while ($result = $getTime->fetch(PDO::FETCH_ASSOC)); ?>
  </div>
  <?php } else { ?>
  <?php } ?>

</div>

<?php

include BASE_PATH . 'views/footer.php';