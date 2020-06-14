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

<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <h2>Select a Squad to Manage its Sessions</h2>
      <form>
        <div class="form-group">
          <label for="squad">Select Squad</label>
          <select class="custom-select" name="squad" id="squad">
            <option value="0">Choose your squad from the menu</option>
            <?php do { ?>
            <option value="<?=$squad['id']?>" <?php if ($selectedSquad == $squad['id']) { ?>selected<?php } ?>>
              <?=htmlspecialchars($squad['name'])?> Squad
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

  <div id="modalArea">
    <div id="output">
      <div class="ajaxPlaceholder"><strong>Session Manager will appear here</strong> <br>Select a squad first</div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("js/attendance/register.js");
$footer->useFluidContainer();
$footer->render();