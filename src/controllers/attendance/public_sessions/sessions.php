<?php

global $db;

$now = new DateTime('now', new DateTimeZone('Europe/London'));
$startWeek = new DateTime('sunday -1 week', new DateTimeZone('UTC'));
$now->setTimezone(new DateTimeZone('UTC'));

if ($now->format('l') == 'Sunday') {
  $startWeek = $now;
}

$endWeek = $startWeek->add(new DateInterval('P7D'));

$week = $startWeek->format('W');
$day = $startWeek->format('d');
$month = $startWeek->format('m');
$year = $startWeek->format('Y');

/*pre([
  $week,
  $day,
  $month,
  $year
]);*/

$sessions = $db->prepare("SELECT * FROM ((`sessions` INNER JOIN squads ON squads.SquadID = sessions.SquadID) INNER JOIN sessionsVenues ON sessionsVenues.VenueID = sessions.VenueID) WHERE DisplayFrom <= ? AND DisplayUntil >= ? ORDER BY SessionDay ASC, StartTime ASC, EndTime ASC");
$sessions->execute([
  $startWeek->format('Y-m-d'),
  $endWeek->format('Y-m-d')
]);

$session = $sessions->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Sessions in <abbr title="ISO-8601 week number of year">week <?=$week?></abbr></h1>
      <p class="lead">A permanent web presence for all training sessions at <?=htmlspecialchars(env('CLUB_NAME'))?></p>

      <p><strong>Please note:</strong> This system cannot currently indicate whether a session is cancelled.</p>

      <p>Showing sessions for the week beginning <strong><?=$startWeek->format('l j F Y')?></strong>.</p>

      <?php if ($session != null) { ?>
      <ul class="list-group">
        <?php do { ?>
        <li class="list-group-item">
          <h2 class="mb-0"><?=htmlspecialchars($session['SquadName'])?> Squad</h2>
          <p class="h2"><small><?=htmlspecialchars($session['SessionName'])?>, <?=htmlspecialchars($session['VenueName'])?></small></p>

          <?php
            $startTime = new DateTime($session['StartTime'], new DateTimeZone('UTC'));
            $endTime = new DateTime($session['EndTime'], new DateTimeZone('UTC'));
          ?>

          <dl class="row mb-0">
            <dt class="col-sm-3">Starts at</dt>
            <dd class="col-sm-9"><?=htmlspecialchars($startTime->format('H:i'))?></dd>

            <dt class="col-sm-3">Ends at</dt>
            <dd class="col-sm-9"><?=htmlspecialchars($endTime->format('H:i'))?></dd>

            <dt class="col-sm-3">Coach(es)</dt>
            <dd class="col-sm-9"><?=htmlspecialchars($session['SquadCoach'])?></dd>

            <?php

            // This is sensitive so hide of logged out

            ?>

            <dt class="col-sm-3">Location</dt>
        <dd class="col-sm-9 mb-0"><?php if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']) { ?><?=htmlspecialchars($session['Location'])?><?php } else { ?>You must be logged in to see the location<?php } ?></dd>
          </dl>
          <?php //pre($session); ?>
        </li>
        <?php } while ($session = $sessions->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
      <?php } else { ?>
      <div class="alert alert-warning">
        <strong>There are no sessions to show this week.</strong>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';

?>