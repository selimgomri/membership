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

$strokes = [
  1 => 'Free',
  2 => 'Back',
  3 => 'Breast',
  4 => 'Butterfly',
  5 => 'IM'
];

$distances = [
  1 => [
    50,
    100,
    200,
    400,
    800,
    1500
  ],
  2 => [
    50,
    100,
    200
  ],
  3 => [
    50,
    100,
    200
  ],
  4 => [
    50,
    100,
    200
  ],
  5 => [
    100,
    200,
    400
  ],
];

$getTime = $db->prepare("SELECT `Time`, `Date`, `Round`, `Stroke`, `Distance`, `Name`, `City`, `GalaName` FROM ((meetResults INNER JOIN meetsWithResults ON meetResults.Meet = meetsWithResults.Meet) LEFT JOIN galas ON meetsWithResults.Gala = galas.GalaID) WHERE Member = ? AND Stroke = ? AND Distance = ? AND meetResults.Course = ? ORDER BY IntTime ASC");
$getTime->execute([$id, 1, 200, 'S']);

$pagetitle = htmlspecialchars($swimmer['MForename'] . ' ' . $swimmer['MSurname']) . ' Times';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("members")?>">Members</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("members/" . $id)?>"><?=htmlspecialchars($swimmer["MForename"])?> <?=htmlspecialchars(mb_substr($swimmer["MSurname"], 0, 1, 'utf-8'))?></a></li>
			<li class="breadcrumb-item active" aria-current="page">Best times</li>
		</ol>
	</nav>

  <h1>Best times</h1>
  <p class="lead">Welcome to our new best times system.</p>
  <p>Our new system uses a results file for every gala so has only been populated with results going back a reasonable period of time.</p>

  <div class="mb-3">
    <h2>Short course</h2>
    <div class="d-none d-lg-block py-1 px-2 bg-primary text-white">
      <div class="row">
        <div class="col-lg-2">
          <strong>Event</strong>
        </div>
        <div class="col-lg-2 text-right">
          <strong>Time</strong>
        </div>
        <div class="col-lg-2">
          <strong>Date</strong>
        </div>
        <div class="col-lg-2">
          <strong>Gala</strong>
        </div>
        <div class="col-lg-2">
          <strong>City</strong>
        </div>
      </div>
    </div>
    <?php $count = 0; ?>
    <?php foreach ($strokes as $strokeCode => $stroke) { ?>
      <?php foreach ($distances[$strokeCode] as $distance) {
        $getTime->execute([$id, $strokeCode, $distance, 'S']);
        $row = $getTime->fetch(PDO::FETCH_ASSOC);
        if ($row != null) { ?>
        <div class="py-1 px-2  <?php if ($count%2 == 0) { ?>bg-light<?php } ?>">
          <div class="row">
            <div class="col-4 col-md-4 col-lg-2 text-truncate">
              <?=$distance?>m <?=$stroke?>
            </div>
            <div class="col-4 col-md-4 col-lg-2 text-right">
              <span class="mono"><?=htmlspecialchars($row['Time'])?></span>
            </div>
            <div class="col-4 col-md-4 col-lg-2 text-truncate text-right text-lg-left">
              <?=htmlspecialchars((new DateTime($row['Date'], new DateTimeZone('Europe/London')))->format("j/n/Y"))?>
            </div>
            <div class="col-8 col-lg-2 text-truncate" title="<?php if ($row['GalaName'] == null) { ?><?=htmlspecialchars($row['Name'])?><?php } else { ?><?=htmlspecialchars($row['GalaName'])?><?php } ?>">
              <?php if ($row['GalaName'] == null) { ?>
              <?=htmlspecialchars($row['Name'])?>
              <?php } else { ?>
              <?=htmlspecialchars($row['GalaName'])?>
              <?php } ?>
            </div>
            <div class="col-4 text-right text-lg-left col-lg-2 text-truncate">
              <?=htmlspecialchars($row['City'])?>
            </div>
          </div>
        </div>
        <?php $count++; } ?>
      <?php } ?>
    <?php } ?>
  </div>

  <div class="mb-3">
    <h2>Long course</h2>
    <div class="d-none d-lg-block py-1 px-2 bg-primary text-white">
      <div class="row">
        <div class="col-lg-2">
          <strong>Event</strong>
        </div>
        <div class="col-lg-2 text-right">
          <strong>Time</strong>
        </div>
        <div class="col-lg-2">
          <strong>Date</strong>
        </div>
        <div class="col-lg-2">
          <strong>Gala</strong>
        </div>
        <div class="col-lg-2">
          <strong>City</strong>
        </div>
      </div>
    </div>
    <?php $count = 0; ?>
    <?php foreach ($strokes as $strokeCode => $stroke) { ?>
      <?php foreach ($distances[$strokeCode] as $distance) {
        $getTime->execute([$id, $strokeCode, $distance, 'L']);
        $row = $getTime->fetch(PDO::FETCH_ASSOC);
        if ($row != null) { ?>
        <div class="py-1 px-2 <?php if ($count%2 == 0) { ?>bg-light<?php } ?>">
          <div class="row">
            <div class="col-4 col-md-4 col-lg-2 text-truncate">
              <?=$distance?>m <?=$stroke?>
            </div>
            <div class="col-4 col-md-4 col-lg-2 text-right">
              <span class="mono"><?=htmlspecialchars($row['Time'])?></span>
            </div>
            <div class="col-4 col-md-4 col-lg-2 text-truncate text-right text-lg-left">
              <?=htmlspecialchars((new DateTime($row['Date'], new DateTimeZone('Europe/London')))->format("j/n/Y"))?>
            </div>
            <div class="col-8 col-lg-2 text-truncate" title="<?php if ($row['GalaName'] == null) { ?><?=htmlspecialchars($row['Name'])?><?php } else { ?><?=htmlspecialchars($row['GalaName'])?><?php } ?>">
              <?php if ($row['GalaName'] == null) { ?>
              <?=htmlspecialchars($row['Name'])?>
              <?php } else { ?>
              <?=htmlspecialchars($row['GalaName'])?>
              <?php } ?>
            </div>
            <div class="col-4 text-right text-lg-left col-lg-2 text-truncate">
              <?=htmlspecialchars($row['City'])?>
            </div>
          </div>
        </div>
        <?php $count++; } ?>
      <?php } ?>
    <?php } ?>
  </div>

</div>

<?php

include BASE_PATH . 'views/footer.php';