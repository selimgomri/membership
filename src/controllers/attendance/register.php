<?php

$preload = true;
$getRegister = false;
$getSessions = false;
$week_to_get = null;

global $db;

$getWeeks = $db->query("SELECT * FROM sessionsWeek ORDER BY WeekDateBeginning DESC LIMIT 4");
$getSquads = $db->query("SELECT DISTINCT squads.SquadID, SquadName FROM squads INNER JOIN sessions ON squads.SquadID = sessions.SquadID ORDER BY SquadFee DESC, SquadName ASC");

$session_init = $session;
$squad_init = $squad;

$pagetitle = "Register";
$title = "Register";

$fluidContainer = true;
$use_white_background = true;
include BASE_PATH . "views/header.php";
include "attendanceMenu.php";

?>

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-auto">
    <h1>Register</h1>
    <p class="lead">Take the register for your Squad</p>
    </div>
    <div class="col">
      <p class="lead text-sm-right">
        The time is <span id="dtOut" class="mono"></span>
      </p>
    </div>
  </div>
  <?php if (isset($_SESSION['return'])) { ?>
  <div class="alert alert-success">
    <?=$_SESSION['return']?>
  </div>
  <?php unset($_SESSION['return']); } ?>

  <form method="post">
    <div class="card mb-3">
      <div class="card-body">
        <h2 class="card-title">Select Session</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="session">Select Week</label>
              <select class="custom-select" name="date" id="date">
                <?php
                  // Get the date of the week beginning
                  $day = date('w');
                  $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                  while ($week = $getWeeks->fetch(PDO::FETCH_ASSOC)) { ?>
                  <?php if ($week_to_get == null) {
                    $week_to_get = $week['WeekID'];
                  } ?>
                <option value="<?=$week['WeekID']?>">
                  Week Beginning <?=date('j F Y', strtotime($week['WeekDateBeginning']))?>
                </option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="squad">Select Squad</label>
              <select class="custom-select" name="squad" id="squad">';
                <?php if ($squad == null) { ?>
                <option value="0">Choose your squad from the menu</option>
                <?php } ?>
                <?php while ($row = $getSquads->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?=$row['SquadID']?>" <?php if ($squad == $row['SquadID']) { ?>selected<?php } ?>>
                  <?=htmlspecialchars($row['SquadName'])?> Squad
                </option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group mb-0">
              <label for="session">Select Session</label>
              <select class="custom-select" id="session" name="session">
                <?php if ($session_init && $squad_init) { ?>
                <?php
                $getRegister = false;
                $getSessions = true;
                include BASE_PATH . 'controllers/ajax/registerSessions.php';
                ?>
                <?php } else { ?>
                <option selected>No squad selected</option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div id="register">
        <?php if ($session_init && $squad_init) { ?>
        <?php
        $getRegister = true;
        $getSessions = false;
        $weekID = $week_to_get;
        include BASE_PATH . 'controllers/ajax/registerSessions.php';
        ?>
        <?php } else { ?>
        <div class="ajaxPlaceholder mb-0">Fill in the details above to load a register.
        </div>
        <?php } ?>
      </div>
    </div>
  </form>
</div>

<script>
function resetRegisterArea() {
  var register = document.getElementById("register");
  register.innerHTML = '<div class="ajaxPlaceholder mb-0">Fill in the details above and we can load the register</div>';
}

function getSessions(firstLoad = false) {
  <?php if ($squad_init == null) {
    $squad = "null";
  } ?>
  var firstLoadSquad = <?=$squad?>;
  <?php if ($session_init == null) {
    $session = "null";
  } ?>
  var fLSession = <?=$session?>;
  var e = document.getElementById("squad");
  var value = e.options[e.selectedIndex].value;
  if (firstLoad === true) {
    value = firstLoadSquad;
  } else {
    fLSession = null;
  }
  console.log(value);
  if (value == "" || value == null) {
    document.getElementById("session").innerHTML = "<option selected>Choose the squad from the menu</option>";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("session").innerHTML = this.responseText;
        console.log(this.responseText);
        resetRegisterArea();
      }
    }
    var target = "<?=autoUrl("attendance/ajax/register/sessions")?>?squadID=" + value + "&selected=" + fLSession;
    console.log(target);
    xmlhttp.open("GET", target, true);
    xmlhttp.send();
  }
}

function getRegister(firstLoad = false) {
  <?php if ($session_init == null) {
    $session = "null";
  } ?>
  var presetSession = <?=$session?>;
  var e = document.getElementById("session");
  var value = e.options[e.selectedIndex].value;
  if (firstLoad === true) {
    value = presetSession;
  }
  var date = document.getElementById("date");
  var dateValue = date.options[date.selectedIndex].value;
  if (value == "") {
    document.getElementById("register").innerHTML =
      '<div class="ajaxPlaceholder mb-0">Fill in the details above to load a register.</div>';
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("register").innerHTML = this.responseText;
      }
    }
    xmlhttp.open("GET", "<?=autoUrl("attendance/ajax/register/sessions")?>?sessionID=" + value + "&date=" + dateValue,
      true);
    xmlhttp.send();
  }
}

<?php if ($session_init != null && $squad_init != null) { ?>
//getSessions(true);
//getRegister(true);
<?php } ?>

document.getElementById("squad").onchange = getSessions;
document.getElementById("session").onchange = getRegister;
</script>

<script>
function updateTime() {
  var datetimeScreenOutput = document.getElementById("dtOut");
  if (datetimeScreenOutput != null) {
    var today = new Date();
    datetimeScreenOutput.textContent = today.toLocaleTimeString(undefined, {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
  }
}

var intervalID = window.setInterval(updateTime, 250);
</script>
<?php
include BASE_PATH . "views/footer.php";