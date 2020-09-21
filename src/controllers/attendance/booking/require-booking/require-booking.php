<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

if (!$user->hasPermission('Admin') && !$user->hasPermission('Coach')) {
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
        <li class="breadcrumb-item active" aria-current="page">Require Booking</li>
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
  <div class="row">
    <div class="col-lg-8">
      <form class="needs-validation" method="post" action="<?= htmlspecialchars(autoUrl('sessions/booking/require-booking')) ?>" novalidate>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['RequireBookingError'])) { ?>
          <div class="alert alert-warning">
            <p class="mb-0">
              <strong>Error</strong>
            </p>
            <p class="mb-0">
              <?= htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['RequireBookingError']) ?>
            </p>
          </div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['RequireBookingError']);
        } ?>

        <div class="form-group">
          <label for="session-text-description">Session</label>
          <input type="text" id="session-text-description" name="session-text-description" readonly class="form-control" value="<?= htmlspecialchars('#' . $session['SessionID'] . ' - ' . $session['SessionName']) ?>">
        </div>

        <input type="hidden" name="session" value="<?= htmlspecialchars($session['SessionID']) ?>">

        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" readonly class="form-control" value="<?= htmlspecialchars($date->format('Y-m-d')) ?>">
        </div>

        <div class="form-group" id="number-limit">
          <div class="custom-control custom-radio">
            <input type="radio" id="unlimited-numbers" name="number-limit" class="custom-control-input" value="0" required>
            <label class="custom-control-label" for="unlimited-numbers">Unlimited numbers</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" id="limited-numbers" name="number-limit" class="custom-control-input" value="1">
            <label class="custom-control-label" for="limited-numbers">Limited numbers</label>
          </div>
        </div>

        <div class="d-none" id="max-places-container">
          <div class="form-group">
            <label for="max-count">Maximum places</label>
            <input type="number" id="max-count" name="max-count" min="1" step="1" class="form-control" value="">
            <div class="invalid-feedback">
              Please provide a positive integer
            </div>
          </div>

          <p>
            If you later reduce the limit on a session to a number lower than the number of bookings, then those who booked a place first will keep their places and the most recent bookings being remove down to the new limit.
          </p>
        </div>

        <div class="form-group">
          <div class="custom-control custom-radio">
            <input type="radio" id="open-to-squads" name="open-to" class="custom-control-input" value="0" required>
            <label class="custom-control-label" for="open-to-squads">Open to this session's scheduled squads</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" id="open-to-all" name="open-to" class="custom-control-input" value="1">
            <label class="custom-control-label" for="open-to-all">Open to all members</label>
          </div>
        </div>

        <p>
          We will generate a register for this session based on bookings rather than squad membership.
        </p>

        <p>
          <button type="submit" class="btn btn-primary">
            Require Booking
          </button>
        </p>

      </form>
    </div>
  </div>

</div>

<script>
  let limitRadio = document.getElementById('number-limit');
  console.log(limitRadio);

  limitRadio.addEventListener('change', event => {
    let maxPlacesContainer = document.getElementById('max-places-container');
    let maxCount = document.getElementById('max-count');
    if (event.target.value == '1') {
      maxPlacesContainer.classList.remove('d-none');
      maxCount.required = true;
    } else {
      maxPlacesContainer.classList.add('d-none');
      maxCount.required = false;
    }
  });
</script>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();