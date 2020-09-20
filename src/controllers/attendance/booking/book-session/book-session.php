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
  // $members = array_unique($members);
  $members = array_map("unserialize", array_unique(array_map("serialize", $members)));
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

        <ul class="list-group" id="member-booking-list">
          <?php foreach ($members as $member) { ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><?= htmlspecialchars($member['fn'] . ' ' . $member['sn']) ?></span>
              <span><button class="btn btn-primary" type="button" data-member-name="<?= htmlspecialchars($member['fn'] . ' ' . $member['sn']) ?>" data-member-id="<?= htmlspecialchars($member['id']) ?>" data-operation="book-place" data-session-id="<?= htmlspecialchars($session['SessionID']) ?>" data-session-name="<?= htmlspecialchars($session['SessionName']) ?> on <?= htmlspecialchars($date->format('j F Y')) ?>" data-session-location="<?= htmlspecialchars($session['Location']) ?>" data-session-date="<?= htmlspecialchars($date->format('Y-m-d')) ?>">Book</button></span>
            </li>
          <?php } ?>
        </ul>

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
          <ul class="list-group" id="all-member-booking-list">
            <?php do {
              $booked = new DateTime($bookedMember['BookedAt'], new DateTimeZone('UTC'));
              $booked->setTimezone(new DateTimeZone('Europe/London'));
            ?>
              <li class="list-group-item" id="<?= htmlspecialchars('member-' . $bookedMember['id'] . '-booking') ?>">
                <div class="row align-items-center">
                  <div class="col">
                    <div>
                      <a class="font-weight-bold" href="<?= htmlspecialchars(autoUrl('members/' . $bookedMember['id'])) ?>">
                        <?= htmlspecialchars($bookedMember['fn'] . ' ' . $bookedMember['sn']) ?>
                      </a>
                    </div>
                    <div>
                      <em>Booked at <?= htmlspecialchars($booked->format('H:i, j F Y')) ?></em>
                    </div>
                  </div>
                  <div class="col-auto">
                    <button class="btn btn-danger" type="button" data-member-name="<?= htmlspecialchars($bookedMember['fn'] . ' ' . $bookedMember['sn']) ?>" data-member-id="<?= htmlspecialchars($bookedMember['id']) ?>" data-operation="cancel-place" data-session-id="<?= htmlspecialchars($session['SessionID']) ?>" data-session-name="<?= htmlspecialchars($session['SessionName']) ?> on <?= htmlspecialchars($date->format('j F Y')) ?>" data-session-location="<?= htmlspecialchars($session['Location']) ?>" data-session-date="<?= htmlspecialchars($date->format('Y-m-d')) ?>">Remove</button>
                  </div>
                </div>
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

<div id="ajaxData" data-booking-ajax-url="<?= htmlspecialchars(autoUrl('sessions/booking/book')) ?>" data-cancellation-ajax-url="<?= htmlspecialchars(autoUrl('sessions/booking/cancel')) ?>"></div>

<script>
  const options = document.getElementById('ajaxData').dataset;

  let mbl = document.getElementById('member-booking-list');
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
                // Cheap and nasty!
                // location.reload();

                // Show success message, dismiss modal, reload booking box


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

  let ambl = document.getElementById('all-member-booking-list');
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
                // Delete member row
                let row = document.getElementById('member-' + document.getElementById('cancel-member-id').value + '-booking');
                if (row) {
                  row.remove();
                }

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
