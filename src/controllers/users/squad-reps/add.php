<?php

$db = app()->db;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$userInfo->execute([$id]);
$info = $userInfo->fetch(PDO::FETCH_ASSOC);

$systemInfo = app()->system;
$leavers = app()->tenant->getKey('LeaversSquad');
if ($leavers == null) {
  $leavers = 0;
}
$getSquads = $db->prepare("SELECT SquadName, SquadID FROM squads WHERE SquadID != ? ORDER BY SquadFee DESC, SquadName ASC");
$getSquads->execute([
  $leavers
]);
$squad = $getSquads->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($info['Forename'] . ' ' . $info['Surname']) . ' Squad Rep Options';

include BASE_PATH . "views/header.php";

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("users")?>">Users</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("users/" . $id)?>"><?=htmlspecialchars(mb_substr($info['Forename'], 0, 1, 'utf-8') . mb_substr($info['Surname'], 0, 1, 'utf-8'))?></a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("users/" . $id . "/rep")?>">Rep Settings</a></li>
      <li class="breadcrumb-item active" aria-current="page">Assign</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>
        Assign a squad to <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?>
      </h1>

      <?php if (isset($_SESSION['AssignSquadError']) && $_SESSION['AssignSquadError']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>
            We were unable to assign that squad to <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['AssignSquadError']);
      } ?>

      <?php if ($squad != null) { ?>
      <form method="post">
        <div class="form-group">
          <label for="squad-select">
            Choose squad
          </label>
          <select class="custom-select" id="squad-select" name="squad-select">
           <option selected>Select a squad</option>
            <?php do { ?>
              <option value="<?=$squad['SquadID']?>">
                <?=htmlspecialchars($squad['SquadName'])?>
              </option>
            <?php } while ($squad = $getSquads->fetch(PDO::FETCH_ASSOC)); ?>
          </select>
        </div>

        <p>
          <button type="submit" class="btn btn-primary">
            Assign squad
          </button>
        </p>
      </form>
      <?php } else { ?>
      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            There are no squads to choose from
          </strong>
        </p>
      </div>
      <?php } ?>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();