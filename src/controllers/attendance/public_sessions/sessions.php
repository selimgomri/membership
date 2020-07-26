<?php

$db = app()->db;
$tenant = app()->tenant;

$now = new DateTime('now', new DateTimeZone('Europe/London'));
$startWeek = new DateTime('monday -1 week', new DateTimeZone('UTC'));
$now->setTimezone(new DateTimeZone('UTC'));

if ($now->format('l') == 'Monday') {
  $startWeek = $now;
}

if (isset($_GET['year']) && isset($_GET['week'])) {
  if (!$startWeek->setISODate((int) $_GET['year'], (int) $_GET['week'], 1)) {
    halt(404);
  }
}

$endWeek = clone $startWeek;

$endWeek->add(new DateInterval('P6D'));

$week = $startWeek->format('W');
$day = $startWeek->format('d');
$month = $startWeek->format('m');
$year = $startWeek->format('Y');

$sessions = $db->prepare("SELECT * FROM ((`sessions` INNER JOIN squads ON squads.SquadID = sessions.SquadID) INNER JOIN sessionsVenues ON sessionsVenues.VenueID = sessions.VenueID) WHERE squads.Tenant = ? AND DisplayFrom <= ? AND DisplayUntil >= ? ORDER BY SessionDay ASC, StartTime ASC, EndTime ASC, SquadFEE DESC, SquadName ASC;");
$sessions->execute([
  $tenant->getId(),
  $startWeek->format('Y-m-d'),
  $endWeek->format('Y-m-d')
]);

$allSessions = $sessions->fetchAll(PDO::FETCH_ASSOC);

$dayNum = (int) $now->format('N') % 7;
$sessionToday = false;

$otherDays = $sundays = [];
foreach ($allSessions as $session) {
  if (($startWeek <= $now && $now <= $endWeek) && !$sessionToday && $dayNum == $session['SessionDay']) {
    $sessionToday = true;
  }
  if ($session['SessionDay'] == 0) {
    $sundays[] = $session;
  } else {
    $otherDays[] = $session;
  }
}
foreach ($sundays as $session) {
  $otherDays[] = $session;
}
$sessions = $otherDays;

$getCoaches = $db->prepare("SELECT Forename fn, Surname sn, coaches.Type code FROM coaches INNER JOIN users ON coaches.User = users.UserID WHERE coaches.Squad = ? ORDER BY coaches.Type ASC, Forename ASC, Surname ASC");

$days = [
  0 => false,
  1 => false,
  2 => false,
  3 => false,
  4 => false,
  5 => false,
  6 => false,
];

$pageHead = [
  'body' => [
    'data-spy="scroll"',
    'data-target="#member-page-menu"'
  ]
];
$pagetitle = "Sessions";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <h1>Sessions in week <?=htmlspecialchars($startWeek->format('W'))?> / <?=htmlspecialchars($startWeek->format('o'))?></h1>
  <p class="lead">Timetable information</p>

  <p>Showing sessions for the week beginning <strong><?=htmlspecialchars($startWeek->format('l j F Y'))?></strong>.</p>

  <?php if ($sessionToday) { ?>
  <p><a href="#day-<?=$dayNum?>">Jump to today</a></p>
  <?php } ?>

  <div class="alert alert-warning">
    <p class="mb-0"><strong>Please note:</strong> This system cannot currently indicate whether  or not a session is cancelled.</p>
  </div>

  <div class="row">
    <div class="col-lg-8 order-2 order-lg-1">
      <?php $currentDay = null; ?>

      <?php if (sizeof($sessions) > 0) { ?>
      <div class="list-group">
        <?php foreach ($sessions as $session) {
          $getCoaches->execute([
            $session['SquadID']
          ]);
          $coaches = $getCoaches->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if ($currentDay != $session['SessionDay']) {
          $currentDay = $session['SessionDay'];
          $day = clone $startWeek;
          $day->add(new DateInterval('P' . ($currentDay+6)%7 . 'D'));
          $days[(int) $day->format('N') % 7] = true; ?>
          <div class="list-group-item bg-primary text-white" id="day-<?=htmlspecialchars($day->format('N') % 7)?>">
            <h2 class="mb-0"><?=htmlspecialchars($day->format('l'))?></h2>
            <p class="lead mb-0"><?=htmlspecialchars($day->format('j F Y'))?></p>
          </div>
        <?php } ?>
        <div class="list-group-item">
          <h3 class="mb-0"><?=htmlspecialchars($session['SquadName'])?> Squad</h3>
          <p class="h3"><small><?=htmlspecialchars($session['SessionName'])?>, <?=htmlspecialchars($session['VenueName'])?></small></p>

          <?php
            $startTime = new DateTime($session['StartTime'], new DateTimeZone('UTC'));
            $endTime = new DateTime($session['EndTime'], new DateTimeZone('UTC'));
          ?>

          <dl class="row mb-0">
            <dt class="col-sm-3">Starts at</dt>
            <dd class="col-sm-9"><?=htmlspecialchars($startTime->format('H:i'))?></dd>

            <dt class="col-sm-3">Ends at</dt>
            <dd class="col-sm-9"><?=htmlspecialchars($endTime->format('H:i'))?></dd>

            <?php
            $duration = $startTime->diff($endTime);
            $hours = (int) $duration->format('%h');
            $mins = (int) $duration->format('%i');
            ?>

            <dt class="col-sm-3">Duration</dt>
            <dd class="col-sm-9"><?php if ($hours > 0) { ?><?=$hours?> hour<?php if ($hours > 1) { ?>s<?php } ?> <?php } ?><?php if ($mins > 0) { ?><?=$mins?> minute<?php if ($mins > 1) { ?>s<?php } ?><?php } ?></dd>

            <dt class="col-sm-3">Squad Coach<?php if (sizeof($coaches) > 0) { ?>es<?php } ?></dt>
            <dd class="col-sm-9">
              <ul class="list-unstyled mb-0">
              <?php for ($i=0; $i < sizeof($coaches); $i++) { ?>
                <li><strong><?=htmlspecialchars($coaches[$i]['fn'] . ' ' . $coaches[$i]['sn'])?></strong>, <?=htmlspecialchars(coachTypeDescription($coaches[$i]['code']))?></li>
              <?php } ?>
              <?php if (sizeof($coaches) == 0) { ?>
                <li>None assigned</li>
              <?php } ?>
              </ul>
            </dd>

            <?php

            // This is sensitive so hide of logged out

            ?>

            <dt class="col-sm-3">Location</dt>
        <dd class="col-sm-9 mb-0"><?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn']) && $_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn']) { ?><?=htmlspecialchars($session['Location'])?><?php } else { ?>You must be logged in to see the location<?php } ?></dd>
          </dl>

          <?php if (!bool($session['MainSequence'])) { ?>
          <div class="alert alert-warning mt-3 mb-0">
            <p class="mb-0"><strong>This session is not for all swimmers in <?=htmlspecialchars($session['SquadName'])?> Squad</strong></p>
            <p class="mb-0">Your coach will tell you if you are to attend this session</p>
          </div>
          <?php } ?>
          <?php //pre($session); ?>
        </div>
        <?php } ?>
      </div>
      <?php } else { ?>
      <div class="alert alert-warning">
        <strong>There are no sessions to show this week.</strong>
      </div>
      <?php } ?>

      <?php $weeks = clone $startWeek; ?>
      <?php $weeks->sub(new DateInterval('P7D')); ?>
      <nav aria-label="Page navigation example">
        <ul class="pagination">
          <li class="page-item">
            <a class="page-link" href="<?=autoUrl("sessions?year=" . $weeks->format('o') . "&week=" . $weeks->format('W'))?>">
              Week <?=(int) $weeks->format('W')?> / <?=$weeks->format('o')?>
            </a>
          </li>
          <?php $weeks->add(new DateInterval('P7D')); ?>
          <li class="page-item">
            <a class="page-link" href="<?=autoUrl("sessions?year=" . $weeks->format('o') . "&week=" . $weeks->format('W'))?>">
              Week <?=(int) $weeks->format('W')?> / <?=$weeks->format('o')?>
            </a>
          </li>
          <?php $weeks->add(new DateInterval('P7D')); ?>
          <li class="page-item">
            <a class="page-link" href="<?=autoUrl("sessions?year=" . $weeks->format('o') . "&week=" . $weeks->format('W'))?>">
              Week <?=(int) $weeks->format('W')?> / <?=$weeks->format('o')?>
            </a>
          </li>
        </ul>
      </nav>
    </div>
    <div class="col order-1 order-lg-2">
      <div class="position-sticky top-3 card mb-3">
        <div class="card-header">
          Jump to day
        </div>
        <div class="list-group list-group-flush" id="member-page-menu">
          <a href="#day-1" <?php if (!$days[1]) { ?>tabindex="-1" aria-disabled="true"<?php } ?> class="list-group-item list-group-item-action <?php if (!$days[1]) { ?>disabled<?php } ?>">
            Monday
          </a>
          <a href="#day-2" <?php if (!$days[2]) { ?>tabindex="-1" aria-disabled="true"<?php } ?> class="list-group-item list-group-item-action <?php if (!$days[2]) { ?>disabled<?php } ?>">
            Tuesday
          </a>
          <a href="#day-3" <?php if (!$days[3]) { ?>tabindex="-1" aria-disabled="true"<?php } ?> class="list-group-item list-group-item-action <?php if (!$days[3]) { ?>disabled<?php } ?>">
            Wednesday
          </a>
          <a href="#day-4" <?php if (!$days[4]) { ?>tabindex="-1" aria-disabled="true"<?php } ?> class="list-group-item list-group-item-action <?php if (!$days[4]) { ?>disabled<?php } ?>">
            Thursday
          </a>
          <a href="#day-5" <?php if (!$days[5]) { ?>tabindex="-1" aria-disabled="true"<?php } ?> class="list-group-item list-group-item-action <?php if (!$days[5]) { ?>disabled<?php } ?>">
            Friday
          </a>
          <a href="#day-6" <?php if (!$days[6]) { ?>tabindex="-1" aria-disabled="true"<?php } ?> class="list-group-item list-group-item-action <?php if (!$days[6]) { ?>disabled<?php } ?>">
            Saturday
          </a>
          <a href="#day-0" <?php if (!$days[0]) { ?>tabindex="-1" aria-disabled="true"<?php } ?> class="list-group-item list-group-item-action <?php if (!$days[0]) { ?>disabled<?php } ?>">
            Sunday
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();

?>