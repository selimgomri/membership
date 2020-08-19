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

include 'session-drop-down.php';
include 'session-register.php';

$fluidContainer = true;
$use_white_background = true;
include BASE_PATH . "views/header.php";
// include "./attendanceMenu.php";

?>

<div id="data-block" data-ajax-url="<?= htmlspecialchars(autoUrl("attendance/register/data-post")) ?>" data-register-sheet-ajax-url="<?= htmlspecialchars(autoUrl("attendance/register/sheet")) ?>" data-session-list-ajax-url="<?= htmlspecialchars(autoUrl("attendance/register/sessions")) ?>"></div>

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

<script>
  const options = document.getElementById('data-block').dataset;
  const registerArea = document.getElementById('register-area');
  const ssf = document.getElementById('session-selection-form');

  ssf.addEventListener('change', (event) => {
    console.log(event);

    // Get values
    let datePicker = document.getElementById('session-date');
    let date = datePicker.value;
    let sessionPicker = document.getElementById('session-select');
    let session = sessionPicker.value;

    if (event.target.id == 'session-date') {
      // Date changed - Reload session list
      sessionPicker.disabled = true;
      sessionPicker.innerHTML = `<option value="none">Loading sessions...</option>`;
      registerArea.innerHTML = '';

      // Get sessions
      var req = new XMLHttpRequest();
      req.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          object = JSON.parse(this.responseText);
          if (object.status == 200) {
            sessionPicker.innerHTML = object.html;
            sessionPicker.disabled = false;
          }
        } else if (this.readyState == 4) {
          // Not ok
        }
      }
      req.open('POST', options.sessionListAjaxUrl, true);
      req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      let sessionString = '';
      if (session != 'none') {
        sessionString = '&session=' + encodeURI(session);
      }
      req.send('date=' + encodeURI(date) + sessionString);
    }

    let sessionUrlString = '';
    if (event.target.id == 'session-select') {
      sessionUrlString = '&session=' + encodeURI(session);

      // Get register for this session
      var req = new XMLHttpRequest();
      req.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          object = JSON.parse(this.responseText);
          if (object.status == 200) {
            registerArea.innerHTML = object.html;
          }
        } else if (this.readyState == 4) {
          // Not ok
        }
      }
      req.open('POST', options.registerSheetAjaxUrl, true);
      req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      req.send('date=' + encodeURI(date) + '&session=' + encodeURI(session));
    }

    window.history.replaceState('string', 'Title', window.location.origin + window.location.pathname + '?date=' + encodeURI(date) + sessionUrlString);
  });

  let regArea = document.getElementById('register-area');
  regArea.addEventListener('click', (event) => {

    if (event) {
      let clickedTarget = event.target;
      if (clickedTarget.tagName == 'SPAN' && clickedTarget.parentNode && clickedTarget.parentNode.tagName == 'BUTTON') {
        clickedTarget = clickedTarget.parentNode;
      }

      if (clickedTarget.dataset.show) {
        let target = document.getElementById(clickedTarget.dataset.show);
        if (target.classList.contains('d-none')) {
          // Currently hidden so lets show
          target.classList.remove('d-none');
          target.classList.add('register-hideable-active');
        } else {
          // Currently displayed so hide
          target.classList.add('d-none');
          target.classList.remove('register-hideable-active');
        }

        // Hide all other displayed ones
        let displayable = document.getElementsByClassName('register-hideable register-hideable-active');
        for (let i = 0; i < displayable.length; i++) {
          let toClose = displayable[i];
          if (toClose != target) {
            toClose.classList.add('d-none');
            toClose.classList.remove('register-hideable-active');
          }
        }
      }
    }

  });

  regArea.addEventListener('change', (event) => {

    if (event) {
      console.info('Change event -');
      console.log(event);
    }

    if (event.target.type == 'checkbox') {
      // Get value and send an AJAX request
      let value = event.target.checked;

      var req = new XMLHttpRequest();
      req.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {

        } else if (this.readyState == 4) {
          // Not ok
          alert('An error occurred. Your change was not saved.');
        }
      }
      req.open('POST', options.ajaxUrl, true);
      req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      req.send('id=' + encodeURI(event.target.dataset.id) + '&state=' + encodeURI(value));
    }

  });
</script>

<?php
$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->useFluidContainer();
$footer->render();
