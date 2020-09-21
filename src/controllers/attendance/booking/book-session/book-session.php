<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

include 'my-members/my-members.php';
include 'all-members/all-members.php';

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

// Number booked in
$getBookedCount = $db->prepare("SELECT COUNT(*) FROM `sessionsBookings` WHERE `Session` = ? AND `Date` = ?");
$getBookedCount->execute([
  $session['SessionID'],
  $date->format('Y-m-d'),
]);
$bookedCount = $getBookedCount->fetchColumn();

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
        <span class="place-numbers-places-booked-string uc-first"><?= htmlspecialchars(mb_ucfirst($numFormatter->format($bookedCount))) ?></span> <?php if ($bookedCount == 1) { ?>member has<?php } else { ?>members have<?php } ?> booked onto this session. <?php if ($session['MaxPlaces']) { ?><span class="place-numbers-places-remaining-string uc-first"><?= htmlspecialchars(mb_ucfirst($numFormatter->format($session['MaxPlaces'] - $bookedCount))) ?></span> <?php if (($session['MaxPlaces'] - $bookedCount) == 1) { ?>place remains<?php } else { ?>places remain<?php } ?> available.<?php } ?>
      </p>

      <h2>Session Details</h2>
      <dl class="row">
        <dt class="col-sm-3">Starts at</dt>
        <dd class="col-sm-9"><?= htmlspecialchars($startTime->format('H:i')) ?></dd>

        <dt class="col-sm-3">Ends at</dt>
        <dd class="col-sm-9"><?= htmlspecialchars($endTime->format('H:i')) ?></dd>

        <dt class="col-sm-3">Duration</dt>
        <dd class="col-sm-9"><?php if ($hours > 0) { ?><?= $hours ?> hour<?php if ($hours > 1) { ?>s<?php } ?> <?php } ?><?php if ($mins > 0) { ?><?= $mins ?> minute<?php if ($mins > 1) { ?>s<?php } ?><?php } ?></dd>

        <?php if ($session['MaxPlaces']) { ?>
          <dt class="col-sm-3">Total places available</dt>
          <dd class="col-sm-9 place-numbers-max-places-int"><?= htmlspecialchars($session['MaxPlaces']) ?></dd>
        <?php } ?>

        <dt class="col-sm-3">Places booked</dt>
        <dd class="col-sm-9 place-numbers-places-booked-int"><?= htmlspecialchars($bookedCount) ?></dd>

        <?php if ($session['MaxPlaces']) { ?>
          <dt class="col-sm-3">Places remaining</dt>
          <dd class="col-sm-9 place-numbers-places-remaining-int"><?= htmlspecialchars(($session['MaxPlaces'] - $bookedCount)) ?></dd>
        <?php } ?>

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

      <!--  -->
      <div id="my-member-booking-container-box">
        <?= getMySessionBookingMembers($session, $date) ?>
      </div>

      <?php if ($user->hasPermission('Admin') || $user->hasPermission('Coach')) { ?>
        <div id="all-member-booking-container-box">
          <?= getAllBookedMembersForSession($session, $date) ?>
        </div>
      <?php } ?>

    </div>
  </div>

</div>

<div class="modal" id="booking-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="booking-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-info text-dark">
        <h5 class="modal-title" id="booking-modal-title">Confirm booking</h5>
        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="member-booking-form">
          <p>
            Please confirm you're booking <strong><span id="booking-modal-member-name"></span></strong> onto <strong><span id="booking-modal-session-name"></span></strong>
          </p>

          <input type="hidden" name="member-id" id="member-id" value="">
          <input type="hidden" name="session-id" id="session-id" value="">
          <input type="hidden" name="session-date" id="session-date" value="">

          <dl class="row">
            <dt class="col-md-4">Charge</dt>
            <dd class="col-md-8">There is no additional charge for this session.</dd>

            <dt class="col-md-4">Location</dt>
            <dd class="col-md-8" id="booking-modal-session-location">Unknown</dd>
          </dl>

          <p class="mb-0">
            This is a new feature and we have not yet added a cancellation facility. Booking a place is final and your club may have penalties for non-attendance at a booked session.
          </p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-dismiss="modal">Don't book</button>
        <button type="submit" class="btn btn-info" form="member-booking-form" id="accept">Confirm booking</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="cancel-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="cancel-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="cancel-modal-title">Cancel booking?</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="cancel-booking-form">
          <p>
            Please confirm you're cancelling <strong><span id="cancel-modal-member-name"></span></strong>'s booking for <strong><span id="cancel-modal-session-name"></span></strong>
          </p>

          <input type="hidden" name="member-id" id="cancel-member-id" value="">
          <input type="hidden" name="session-id" id="cancel-session-id" value="">
          <input type="hidden" name="session-date" id="cancel-session-date" value="">

          <dl class="row">
            <dt class="col-md-4">Charge</dt>
            <dd class="col-md-8">There is no additional charge for this session so no refund or cancellation amount to apply.</dd>

            <dt class="col-md-4">Location</dt>
            <dd class="col-md-8" id="cancel-modal-session-location">Unknown</dd>
          </dl>

          <p class="mb-0">
            We will send an automatic email informing the member their booking has been cancelled.
          </p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-dismiss="modal">Don't cancel</button>
        <button type="submit" class="btn btn-danger" form="cancel-booking-form" id="accept">Cancel booking</button>
      </div>
    </div>
  </div>
</div>

<div id="ajaxData" data-booking-ajax-url="<?= htmlspecialchars(autoUrl('sessions/booking/book')) ?>" data-cancellation-ajax-url="<?= htmlspecialchars(autoUrl('sessions/booking/cancel')) ?>" data-my-member-reload-ajax-url="<?= htmlspecialchars(autoUrl('sessions/booking/my-booking-info?session=' . urlencode($_GET['session']) . '&date=' . urlencode($_GET['date']))) ?>" data-all-member-reload-ajax-url="<?= htmlspecialchars(autoUrl('sessions/booking/all-booking-info?session=' . urlencode($_GET['session']) . '&date=' . urlencode($_GET['date']))) ?>"></div>

<script>
  const options = document.getElementById('ajaxData').dataset;

  function updateBookingNumbers(stats) {
    // Update numbers
    if (stats.placesTotal) {
      let intSpans = document.getElementsByClassName('place-numbers-max-places-int');
      let stringSpans = document.getElementsByClassName('place-numbers-max-places-string');

      for (let element of intSpans) {
        element.textContent = stats.placesTotal.int;
      }

      for (let element of stringSpans) {
        if (element.classList.contains('uc-first')) {
          element.textContent = stats.placesTotal.string.charAt(0).toUpperCase() + stats.placesTotal.string.slice(1);
        } else {
          element.textContent = stats.placesTotal.string;
        }
      }
    }

    if (stats.placesBooked) {
      let intSpans = document.getElementsByClassName('place-numbers-places-booked-int');
      let stringSpans = document.getElementsByClassName('place-numbers-places-booked-string');

      for (let element of intSpans) {
        element.textContent = stats.placesBooked.int;
      }

      for (let element of stringSpans) {
        if (element.classList.contains('uc-first')) {
          element.textContent = stats.placesBooked.string.charAt(0).toUpperCase() + stats.placesBooked.string.slice(1);
        } else {
          element.textContent = stats.placesBooked.string;
        }
      }
    }

    if (stats.placesRemaining) {
      let intSpans = document.getElementsByClassName('place-numbers-places-remaining-int');
      let stringSpans = document.getElementsByClassName('place-numbers-places-remaining-string');

      for (let element of intSpans) {
        element.textContent = stats.placesRemaining.int;
      }

      for (let element of stringSpans) {
        if (element.classList.contains('uc-first')) {
          element.textContent = stats.placesRemaining.string.charAt(0).toUpperCase() + stats.placesRemaining.string.slice(1);
        } else {
          element.textContent = stats.placesRemaining.string;
        }
      }
    }
  }

  let mbl = document.getElementById('my-member-booking-container-box');
  if (mbl) {
    mbl.addEventListener('click', event => {
      if (event.target.tagName === 'BUTTON' && event.target.dataset.operation === 'book-place') {
        document.getElementById('booking-modal-member-name').textContent = event.target.dataset.memberName;
        document.getElementById('booking-modal-session-name').textContent = event.target.dataset.sessionName;
        document.getElementById('booking-modal-session-location').textContent = event.target.dataset.sessionLocation;

        document.getElementById('member-id').value = event.target.dataset.memberId;
        document.getElementById('session-id').value = event.target.dataset.sessionId;
        document.getElementById('session-date').value = event.target.dataset.sessionDate;

        $('#booking-modal').modal('show');

        let form = document.getElementById('member-booking-form');
        form.addEventListener('submit', event => {
          event.preventDefault();
          let formData = new FormData(form);

          // console.log(formData);
          var req = new XMLHttpRequest();
          req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              let json = JSON.parse(this.responseText);
              if (json.status == 200) {
                // Reload booking box etc
                // console.log(formData);

                // ------------------------------------------------------------
                // Reload booking box etc via ajax request
                // ------------------------------------------------------------
                var dataReload = new XMLHttpRequest();
                dataReload.onreadystatechange = function() {
                  if (this.readyState == 4 && this.status == 200) {
                    let json = JSON.parse(this.responseText);
                    if (json.status == 200) {

                      mbl.innerHTML = json.html;
                      updateBookingNumbers(json.stats);

                    } else {
                      alert(json.error);
                    }
                  } else if (this.readyState == 4) {
                    // Not ok
                    alert('An error occurred and we could not parse the submission.');
                  }
                }
                dataReload.open('GET', options.myMemberReloadAjaxUrl, true);
                dataReload.setRequestHeader('Accept', 'application/json');
                dataReload.send();
                // ------------------------------------------------------------
                // ENDS Reload booking box etc via ajax request
                // ------------------------------------------------------------

                // Show success message, dismiss modal, reload booking box
                $('#booking-modal').modal('hide');

              } else {
                alert(json.error);
              }
            } else if (this.readyState == 4) {
              // Not ok
              alert('An error occurred and we could not parse the submission.');
            }
          }
          req.open('POST', options.bookingAjaxUrl, true);
          req.setRequestHeader('Accept', 'application/json');
          req.send(formData);
        });
      }
    });
  }

  let ambl = document.getElementById('all-member-booking-container-box');
  if (ambl) {
    ambl.addEventListener('click', event => {
      if (event.target.tagName === 'BUTTON' && event.target.dataset.operation === 'cancel-place') {
        document.getElementById('cancel-modal-member-name').textContent = event.target.dataset.memberName;
        document.getElementById('cancel-modal-session-name').textContent = event.target.dataset.sessionName;
        document.getElementById('cancel-modal-session-location').textContent = event.target.dataset.sessionLocation;

        document.getElementById('cancel-member-id').value = event.target.dataset.memberId;
        document.getElementById('cancel-session-id').value = event.target.dataset.sessionId;
        document.getElementById('cancel-session-date').value = event.target.dataset.sessionDate;

        $('#cancel-modal').modal('show');

        let form = document.getElementById('cancel-booking-form');
        form.addEventListener('submit', event => {
          event.preventDefault();
          let formData = new FormData(form);

          // console.log(formData);
          var req = new XMLHttpRequest();
          req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              let json = JSON.parse(this.responseText);
              if (json.status == 200) {

                // ------------------------------------------------------------
                // Reload booking box etc via ajax request
                // ------------------------------------------------------------
                var dataReload = new XMLHttpRequest();
                dataReload.onreadystatechange = function() {
                  if (this.readyState == 4 && this.status == 200) {
                    let json = JSON.parse(this.responseText);
                    if (json.status == 200) {

                      ambl.innerHTML = json.html;
                      updateBookingNumbers(json.stats);

                    } else {
                      alert(json.error);
                    }
                  } else if (this.readyState == 4) {
                    // Not ok
                    alert('An error occurred and we could not parse the submission.');
                  }
                }
                dataReload.open('GET', options.allMemberReloadAjaxUrl, true);
                dataReload.setRequestHeader('Accept', 'application/json');
                dataReload.send();
                // ------------------------------------------------------------
                // ENDS Reload booking box etc via ajax request
                // ------------------------------------------------------------

                // Hide modal
                $('#cancel-modal').modal('hide');

              } else {
                alert(json.error);
              }
            } else if (this.readyState == 4) {
              // Not ok
              alert('An error occurred and we could not parse the submission.');
            }
          }
          req.open('POST', options.cancellationAjaxUrl, true);
          req.setRequestHeader('Accept', 'application/json');
          req.send(formData);
        });
      }
    });
  }
</script>

<?php

$footer = new \SCDS\Footer();
$footer->render();
