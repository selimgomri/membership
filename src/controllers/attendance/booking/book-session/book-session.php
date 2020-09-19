<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

if (!$user->hasPermission('Admin') || !$user->hasPermission('Coach')) {
  halt(404);
}

if (!isset($_GET['session']) && !isset($_GET['date'])) halt(404);

$date = null;
try {
  $date = new DateTime($_GET['date'], new DateTimeZone('Europe/London'));
} catch (Exception $e) {
  halt(404);
}

// Get session
$getSession = $db->prepare("SELECT `SessionID`, `SessionName`, `DisplayFrom`, `DisplayUntil`, `StartTime`, `EndTime`, `VenueName`, `Location`, `SessionDay` FROM `sessions` INNER JOIN `sessionsVenues` ON `sessions`.`VenueID` = `sessionsVenues`.`VenueID` WHERE `sessions`.`SessionID` = ? AND `sessions`.`Tenant` = ? AND DisplayFrom <= ? AND DisplayUntil >= ?");
$getSession->execute([
  $_GET['session'],
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

$pagetitle = 'Session Booking';
include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('sessions')) ?>">Sessions</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('sessions/booking')) ?>">Booking</a></li>
        <li class="breadcrumb-item active" aria-current="page">SESSION</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col">
        <h1>
          Require booking for <?= htmlspecialchars($session['SessionName']) ?> on <?= htmlspecialchars($date->format('j F Y')) ?>
        </h1>
        <p class="lead mb-0">
          Book numbers limited or pay as you go sessions
        </p>
      </div>
    </div>

  </div>
</div>

<div class="container">


</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
