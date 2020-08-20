<?php

$db = app()->db;
$tenant = app()->tenant;

$getWeeks = $db->prepare("SELECT * FROM sessionsWeek WHERE Tenant = ? ORDER BY WeekDateBeginning DESC LIMIT 4");
$getWeeks->execute([
  $tenant->getId()
]);
$getSquads = $db->prepare("SELECT squads.SquadID, SquadName FROM squads WHERE squads.Tenant = ? ORDER BY SquadFee DESC, SquadName ASC");
$getSquads->execute([
  $tenant->getId()
]);

$pagetitle = "Register";
$title = "Register";

// Sort out dates
$dateToday = new DateTime('now', new DateTimeZone('Europe/London'));
$date = new DateTime('now', new DateTimeZone('Europe/London'));
if (isset($_GET['date'])) {
  try {
    $userDate = new DateTime($_GET['date'], new DateTimeZone('Europe/London'));
    $date = $userDate;
  } catch (Exception $e) {
    // Ignore
  }
}

// Get sessions
// OBJECTS
// $sessions = TrainingSession::list([
//   'date' => $date->format("Y-m-d"),
// ]);
$sessionId = null;
if (isset($_GET['session'])) {
  $sessionId = (int) $_GET['session'];
}

$socketDataInit = 'https://production-apis.tenant-services.membership.myswimmingclub.uk';
if (bool(getenv("IS_DEV"))) {
  $socketDataInit = 'https://apis.tenant-services.membership.myswimmingclub.uk';
}

include 'session-drop-down.php';
include 'session-register.php';

$fluidContainer = true;
$use_white_background = true;
include BASE_PATH . "views/header.php";
// include "./attendanceMenu.php";

?>

<div id="data-block" data-ajax-url="<?= htmlspecialchars(autoUrl("attendance/register/data-post")) ?>" data-register-sheet-ajax-url="<?= htmlspecialchars(autoUrl("attendance/register/sheet")) ?>" data-session-list-ajax-url="<?= htmlspecialchars(autoUrl("attendance/register/sessions")) ?>" data-socket-init="<?= htmlspecialchars($socketDataInit) ?>"></div>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-fluid">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('attendance')) ?>">Attendance</a></li>
        <li class="breadcrumb-item active" aria-current="page">Register</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col">
        <h1>
          Take a register
        </h1>
        <p class="lead mb-0">
          Take a register for any session
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">

  <div class="card mb-3">
    <div class="card-header">
      Select session
    </div>
    <div class="card-body">
      <form id="session-selection-form" class="form-row" class="needs-validation" novalidate>
        <div class="col-md">
          <div class="form-group mb-0">
            <label for="session-date">Date</label>
            <input type="date" name="session-date" id="session-date" class="form-control" value="<?= htmlspecialchars($date->format("Y-m-d")) ?>" max="<?= htmlspecialchars($dateToday->format("Y-m-d")) ?>" required>
            <div class="invalid-feedback">
              Please supply a valid date
            </div>
            <div class="mb-3 d-md-none"></div>
          </div>
        </div>
        <div class="col">
          <div class="form-group mb-0">
            <label for="session-select">Select a session</label>
            <select class="custom-select" name="session-select" id="session-select" required>
              <?= registerSessionSelectGenerator($date, $sessionId) ?>
            </select>
            <div class="invalid-feedback">
              Please select a session
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div id="register-area">
    <?php if ($sessionId) {
      registerSheetGenerator($date, $sessionId);
    } ?>
  </div>

</div>

<?php
$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
if (bool(getenv("IS_DEV"))) {
  $footer->addExternalJs('https://apis.tenant-services.membership.myswimmingclub.uk/socket.io/socket.io.js');
} else {
  $footer->addExternalJs('https://production-apis.tenant-services.membership.myswimmingclub.uk/socket.io/socket.io.js');
}
$footer->addJs("public/js/attendance/register/register.js");
$footer->useFluidContainer();
$footer->render();
