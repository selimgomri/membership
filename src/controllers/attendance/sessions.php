<?php

$selectedSquad = null;
if (isset($_GET['squad'])) {
  $selectedSquad = $_GET['squad'];
}

$db = app()->db;
$tenant = app()->tenant;

$squads = $db->prepare("SELECT SquadName `name`, SquadID id FROM squads WHERE Tenant = ? ORDER BY SquadFee DESC, `name` ASC");
$squads->execute([
  $tenant->getId()
]);
$squad = $squads->fetch(PDO::FETCH_ASSOC);

$fluidContainer = true;


/*$epoch = date(DATE_ATOM, mktime(0, 0, 0, 1, 1, 1970));
$displayUntil = date(strtotime());
echo $epoch . "<br>";
if ($displayUntil < $epoch) {
  $displayUntil = null;
  echo "TRUE";
}*/

include BASE_PATH . 'views/header.php';
include "attendanceMenu.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-fluid">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('attendance')) ?>">Attendance</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manage Sessions</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Manage Sessions
        </h1>
        <p class="lead mb-0">
          Add or end scheduled squad sessions.
        </p>
      </div>
    </div>
  </div>
</div>

<div id="ajax-info" data-page-url="<?= htmlspecialchars(autoUrl('attendance/sessions')) ?>" data-ajax-url="<?= htmlspecialchars(autoUrl('attendance/sessions/ajax/handler')) ?>"></div>

<div class="container-fluid">
  <div class="row">

    <div class="col-md-6 col-lg-8 pb-3">

      <div class="card card-body h-100">
        <h2>Select a Squad to Manage its Sessions</h2>
        <form>
          <div class="form-group">
            <label for="squad">Select Squad</label>
            <select class="custom-select" name="squad" id="squad">
              <option value="0">Choose your squad from the menu</option>
              <?php do { ?>
                <option value="<?= $squad['id'] ?>" <?php if ($selectedSquad == $squad['id']) { ?>selected<?php } ?>>
                  <?= htmlspecialchars($squad['name']) ?>
                </option>
              <?php } while ($squad = $squads->fetch(PDO::FETCH_ASSOC)); ?>
            </select>
          </div>
        </form>
        <p class="mb-0">
          Then select from the options below to either View Sessions or Add a New Session for the squad
        </p>
      </div>

    </div>

    <div class="col pb-3">
      <aside class="card card-body h-100">

        <h2>Add a one-off session</h2>
        <p class="lead">
          It's easy to add a one off session for multiple squads.
        </p>

        <p class="mb-0">
          <a href="<?= htmlspecialchars(autoUrl('attendance/sessions/new')) ?>" class="btn btn-primary">Add one off session</a>
        </p>

      </aside>
    </div>

  </div>

  <div id="modalArea">
    <div id="output">
      <div class="ajaxPlaceholder"><strong>Session Manager will appear here</strong> <br>Select a squad first</div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/attendance/sessions.js");
$footer->useFluidContainer();
$footer->render();
