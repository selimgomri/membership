<?php

$noSquad = true;
if (isset($_GET['squad'])) {
  $noSquad = false;
}

global $db;
global $systemInfo;
$leavers = $systemInfo->getSystemOption('LeaversSquad');
if ($leavers == null) {
  $leavers = 0;
}

$squads = $db->prepare("SELECT SquadName `name`, SquadID `id` FROM squads WHERE `SquadID` != ? ORDER BY SquadFee DESC, `name` ASC");
$squads->execute([
  $leavers
]);

$pagetitle = "Membership category editor";
$fluidContainer = true;

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <h1>Membership category editor</h1>
  <p class="lead">Quickly modify Swim England categories for members.</p>

  <form method="post" id="cat-form">
    <div class="cell">
      <h2>Select a squad</h2>
      <p class="lead">Select a squad to edit</p>
      <div class="form-group mb-0">
        <label for="squad-select">
          Choose squad
        </label>
        <select class="custom-select" id="squad-select" name="squad-select" data-gala-id="<?=htmlspecialchars($id)?>">
          <?php if ($noSquad) { ?>
          <option selected>Select a squad</option>
          <?php } ?>
          <?php while ($s = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
          <option value="<?=$s['id']?>" <?php if ((int) $s['id'] == $squad) { ?>selected<?php } ?>>
            <?=htmlspecialchars($s['name'])?>
          </option>
          <?php } ?>
        </select>
      </div>
    </div>

    <p>
      <button type="submit" class="btn btn-success">
        Save changes
      </button>
    </p>

  </form>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();