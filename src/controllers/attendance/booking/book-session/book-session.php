<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

if (!isset($_GET['session']) && !isset($_GET['date'])) halt(404);

$date = null;
try {
  $date = new DateTime($_GET['date'], new DateTimeZone('Europe/London'));
} catch (Exception $e) {
  halt(404);
}

// Get session
$getSession = $db->prepare("SELECT `SessionID`, `SessionName`, `DisplayFrom`, `DisplayUntil`, `StartTime`, `EndTime`, `VenueName`, `Location`, `SessionDay`, `MaxPlaces`, `AllSquads` FROM `sessionsBookable` INNER JOIN `sessions` ON sessionsBookable.Session = sessions.SessionID INNER JOIN `sessionsVenues` ON `sessions`.`VenueID` = `sessionsVenues`.`VenueID` WHERE `sessionsBookable`.`Session` = ? AND `sessionsBookable`.`Date` = ? AND `sessions`.`Tenant` = ? AND DisplayFrom <= ? AND DisplayUntil >= ?");
$getSession->execute([
  $_GET['session'],
  $date->format('Y-m-d'),
  $tenant->getId(),
  $date->format('Y-m-d'),
  $date->format('Y-m-d'),
]);

$session = $getSession->fetch(PDO::FETCH_ASSOC);

if (!$session) {
  halt(404);
}

// Validate session happens on this day
$dayOfWeek = $date->format('w');

if ($session['SessionDay'] != $dayOfWeek) {
  halt(404);
}

$numFormatter = new NumberFormatter("en-GB", NumberFormatter::SPELLOUT);

// Get bookable members
$members = [];
if (bool($session['AllSquads'])) {
  $getMembers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM members WHERE UserID = ? ORDER BY fn ASC, sn ASC");
  $getMembers->execute([
    $user->getId(),
  ]);
  $members = $getMembers->fetchAll(PDO::FETCH_ASSOC);
} else {
  // Get session squads
  $getSquads = $db->prepare("SELECT `Squad` FROM `sessionsSquads` WHERE `Session` = ?");
  $getSquads->execute([
    $session['SessionID'],
  ]);

  while ($squad = $getSquads->fetchColumn()) {
    // Get members for this user in this squad
    $getMembers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM members INNER JOIN squadMembers ON members.MemberID = squadMembers.Member WHERE UserID = ? AND squadMembers.Squad = ? ORDER BY fn ASC, sn ASC");
    $getMembers->execute([
      $user->getId(),
      $squad,
    ]);

    while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)) {
      $members[] = $member;
    }
  }

  // Remove duplicated
  $members = array_unique($members);
}

// Number booked in
$getBookedCount = $db->prepare("SELECT COUNT(*) FROM `sessionsBookings` WHERE `Session` = ? AND `Date` = ?");
$getBookedCount->execute([
  $session['SessionID'],
  $date->format('Y-m-d'),
]);
$bookedCount = $getBookedCount->fetchColumn();

$getBookedMembers = null;
if ($user->hasPermission('Admin') || $user->hasPermission('Coach')) {
  $getBookedMembers = $db->prepare("SELECT Member id, MForename fn, MSurname sn, BookedAt FROM sessionsBookings INNER JOIN members ON sessionsBookings.Member = members.MemberID WHERE sessionsBookings.Session = ? AND sessionsBookings.Date = ? ORDER BY BookedAt ASC, MForename ASC, MSurname ASC;");
  $getBookedMembers->execute([
    $session['SessionID'],
    $date->format('Y-m-d'),
  ]);
}

$sessionDateTime = DateTime::createFromFormat('Y-m-d-H:i:s', $date->format('Y-m-d') .  '-' . $session['StartTime']);
$startTime = new DateTime($session['StartTime'], new DateTimeZone('UTC'));
$endTime = new DateTime($session['EndTime'], new DateTimeZone('UTC'));
$duration = $startTime->diff($endTime);
$hours = (int) $duration->format('%h');
$mins = (int) $duration->format('%i');

$getCoaches = $db->prepare("SELECT Forename fn, Surname sn, coaches.Type code FROM coaches INNER JOIN users ON coaches.User = users.UserID WHERE coaches.Squad = ? ORDER BY coaches.Type ASC, Forename ASC, Surname ASC");

$getSessionSquads = $db->prepare("SELECT SquadName, ForAllMembers, SquadID FROM `sessionsSquads` INNER JOIN `squads` ON sessionsSquads.Squad = squads.SquadID WHERE sessionsSquads.Session = ? ORDER BY SquadFee DESC, SquadName ASC;");
$getSessionSquads->execute([
  $session['SessionID'],
]);
$squadNames = $getSessionSquads->fetchAll(PDO::FETCH_ASSOC);

$pagetitle = 'Session Booking';
include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('sessions')) ?>">Sessions</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('sessions/booking')) ?>">Booking</a></li>
        <li class="breadcrumb-item active" aria-current="page">Book</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($session['SessionName']) ?> on <?= htmlspecialchars($date->format('j F Y')) ?>
        </h1>
        <p class="lead mb-0">
          <?php if ($session['MaxPlaces']) { ?>There are <?= htmlspecialchars($numFormatter->format($session['MaxPlaces'])) ?> places at this session<?php } else { ?>There are unlimited places at this session<?php } ?>
        </p>
      </div>
      <div class="col text-right">
        <?php if ($user->hasPermission('Admin') || $user->hasPermission('Coach')) { ?>
          <a href="<?= htmlspecialchars(autoUrl('sessions/booking/edit?session=' . urlencode($session['SessionID']) . '&date=' . urlencode($date->format('Y-m-d')))) ?>" class="btn btn-primary">
            Edit bookable session details
          </a>
        <?php } ?>
      </div>
    </div>

  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8">
      <p class="lead">
        <?= htmlspecialchars(mb_ucfirst($numFormatter->format($bookedCount))) ?> <?php if ($bookedCount == 1) { ?>member has<?php } else { ?>members have<?php } ?> booked onto this session. <?php if ($session['MaxPlaces']) { ?><?= htmlspecialchars(mb_ucfirst($numFormatter->format($session['MaxPlaces'] - $bookedCount))) ?><?php } ?> <?php if (($session['MaxPlaces'] - $bookedCount) == 1) { ?>place remains<?php } else { ?>places remain<?php } ?> available.
      </p>

      <h2>Session Details</h2>
      <dl class="row">
        <dt class="col-sm-3">Starts at</dt>
        <dd class="col-sm-9"><?= htmlspecialchars($startTime->format('H:i')) ?></dd>

        <dt class="col-sm-3">Ends at</dt>
        <dd class="col-sm-9"><?= htmlspecialchars($endTime->format('H:i')) ?></dd>

        <dt class="col-sm-3">Duration</dt>
        <dd class="col-sm-9"><?php if ($hours > 0) { ?><?= $hours ?> hour<?php if ($hours > 1) { ?>s<?php } ?> <?php } ?><?php if ($mins > 0) { ?><?= $mins ?> minute<?php if ($mins > 1) { ?>s<?php } ?><?php } ?></dd>

        <dt class="col-sm-3">Total places available</dt>
        <dd class="col-sm-9"><?= htmlspecialchars($session['MaxPlaces']) ?></dd>

        <dt class="col-sm-3">Places booked</dt>
        <dd class="col-sm-9"><?= htmlspecialchars($bookedCount) ?></dd>

        <dt class="col-sm-3">Places remaining</dt>
        <dd class="col-sm-9"><?= htmlspecialchars(($session['MaxPlaces'] - $bookedCount)) ?></dd>

        <?php for ($i = 0; $i < sizeof($squadNames); $i++) {
          $getCoaches->execute([
            $squadNames[$i]['SquadID'],
          ]);
          $coaches = $getCoaches->fetchAll(PDO::FETCH_ASSOC);
        ?>
          <dt class="col-sm-3"><?= htmlspecialchars($squadNames[$i]['SquadName']) ?> Coach<?php if (sizeof($coaches) > 0) { ?>es<?php } ?></dt>
          <dd class="col-sm-9">
            <ul class="list-unstyled mb-0">
              <?php for ($i = 0; $i < sizeof($coaches); $i++) { ?>
                <li><strong><?= htmlspecialchars($coaches[$i]['fn'] . ' ' . $coaches[$i]['sn']) ?></strong>, <?= htmlspecialchars(coachTypeDescription($coaches[$i]['code'])) ?></li>
              <?php } ?>
              <?php if (sizeof($coaches) == 0) { ?>
                <li>None assigned</li>
              <?php } ?>
            </ul>
          </dd>
        <?php } ?>

        <dt class="col-sm-3">Location</dt>
        <dd class="col-sm-9 mb-0"><?= htmlspecialchars($session['Location']) ?></dd>
      </dl>

      <h2>Book</h2>
      <p class="lead">
        Book a place for a member linked to your account.
      </p>

      <?php if (sizeof($members) > 0) { ?>

      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>You have no members</strong>
          </p>
          <p class="mb-0">
            Only members can be booked onto sessions.
          </p>
        </div>
      <?php } ?>

      <?php if ($user->hasPermission('Admin') || $user->hasPermission('Coach')) { ?>
        <h2>Booked members</h2>
        <p class="lead">
          Members who have booked a place for this session.
        </p>

        <?php if ($bookedMember = $getBookedMembers->fetch(PDO::FETCH_ASSOC)) { ?>
          <ul class="list-group">
            <?php do {
              $booked = new DateTime($bookedMember['BookedAt'], new DateTimeZone('UTC'));
              $booked->setTimezone(new DateTimeZone('Europe/London'));
            ?>
              <li class="list-group-items">
                <a class="font-weight-bold d-block" href="<?= htmlspecialchars(autoUrl('members/' . $bookedMember['id'])) ?>"><?= htmlspecialchars($bookedMember['fn'] . ' ' . $bookedMember['sn']) ?></a>
                <em>Booked at <?= htmlspecialchars($booked->format('H:i, j F Y')) ?></em>
              </li>
            <?php } while ($bookedMember = $getBookedMembers->fetch(PDO::FETCH_ASSOC)); ?>
          </ul>
        <?php } else { ?>
          <div class="alert alert-info">
            <p class="mb-0">
              <strong>There are no members booked on this session yet</strong>
            </p>
          </div>
        <?php } ?>
      <?php } ?>

    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
