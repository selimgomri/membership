<?php

$preload = true;
$getRegister = false;
$getSessions = false;
$week_to_get = null;

$db = app()->db;
$tenant = app()->tenant;

$getWeeks = $db->prepare("SELECT * FROM sessionsWeek WHERE Tenant = ? ORDER BY WeekDateBeginning DESC LIMIT 4");
$getWeeks->execute([
  $tenant->getId()
]);
$getSquads = $db->prepare("SELECT DISTINCT squads.SquadID, SquadName FROM squads INNER JOIN sessions ON squads.SquadID = sessions.SquadID WHERE squads.Tenant = ? ORDER BY SquadFee DESC, SquadName ASC");
$getSquads->execute([
  $tenant->getId()
]);

$getSquadsCount = $db->prepare("SELECT COUNT(DISTINCT squads.SquadID) FROM squads INNER JOIN sessions ON squads.SquadID = sessions.SquadID WHERE squads.Tenant = ? ORDER BY SquadFee DESC, SquadName ASC");
$getSquadsCount->execute([
  $tenant->getId()
]);
$squadCount = $getSquadsCount->fetchColumn();

$session_init = $session;
$squad_init = $squad;

$pagetitle = "Register";
$title = "Register";

$dateToday = new DateTime('now', new DateTimeZone('Europe/London'));

$fluidContainer = true;
$use_white_background = true;
include BASE_PATH . "views/header.php";
include "attendanceMenu.php";

?>

<div id="data-block" data-ajax-url="<?=htmlspecialchars(autoUrl("attendance/ajax/register/sessions"))?>" data-session-init="<?=htmlspecialchars($session_init)?>" data-squad-init="<?=htmlspecialchars($squad_init)?>" data-page-url="<?=htmlspecialchars(autoUrl("attendance/register"))?>"></div>

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-auto">
    <h1>Register</h1>
    <p class="lead">Take the register for your squad</p>
    </div>
    <div class="col">
      <p class="lead text-sm-right">
        <span id="dtOut" class="mono"></span>
      </p>
    </div>
  </div>
  <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['return'])) { ?>
  <div class="alert alert-success">
    <?=$_SESSION['TENANT-' . app()->tenant->getId()]['return']?>
  </div>
  <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['return']); } ?>

  <?php if ($squadCount == 0) { ?>
    <div class="alert alert-warning">
      <p class="mb-0">
        <strong>No squads have sessions</strong>
      </p>
      <p class="mb-0">
        Your club needs to add sessions before you can take a register.
      </p>
    </div>
  <?php } ?>

  <form method="post">
    <div class="card mb-3">
      <div class="card-body">
        <h2 class="card-title">Select Session</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="date">Select date</label>
              <input type="date" class="form-control" name="date" id="date" value="<?=htmlspecialchars($dateToday->format("Y-m-d"))?>" max="<?=htmlspecialchars($dateToday->format("Y-m-d"))?>">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="squad">Select squad</label>
              <select class="custom-select" name="squad" id="squad">
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
              <label for="session">Select session</label>
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

<?php
$footer = new \SCDS\Footer();
$footer->addJs("public/js/attendance/register.js");
$footer->useFluidContainer();
$footer->render();