<?php

$db = app()->db;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$userInfo->execute([$id]);
$info = $userInfo->fetch(PDO::FETCH_ASSOC);

$getSquads = $db->prepare("SELECT SquadName, SquadID FROM squadReps INNER JOIN squads ON squads.SquadID = squadReps.Squad WHERE squadReps.User = ?");
$getSquads->execute([
  $id
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
      <li class="breadcrumb-item"><a href="<?=autoUrl("users/" . $id)?>"><?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Rep Settings</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>
        <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?><br><small>Squad Rep Settings</small>
      </h1>

      <?php if (isset($_SESSION['AssignSquadSuccess']) && $_SESSION['AssignSquadSuccess']) { ?>
      <div class="alert alert-success">
        <p class="mb-0">
          <strong>
            We've assigned that squad to <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['AssignSquadSuccess']);
      } ?>

      <?php if (isset($_SESSION['RemoveSquadSuccess']) && $_SESSION['RemoveSquadSuccess']) { ?>
      <div class="alert alert-success">
        <p class="mb-0">
          <strong>
            We've removed that squad from <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['RemoveSquadSuccess']);
      } ?>

      <?php if (isset($_SESSION['RemoveSquadError']) && $_SESSION['RemoveSquadError']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>
            We could not remove that squad from <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['RemoveSquadError']);
      } ?>

      <?php if ($squad != null) { ?>
      <p>
        This user is a rep for the following squads
      </p>
      <ul class="list-group mb-3">
        <?php do { ?>
        <li class="list-group-item">
          <div class="row align-items-center justify-content-between">
            <div class="col">
              <?=htmlspecialchars($squad['SquadName'])?>
            </div>
            <div class="col text-right">
              <span>
                <a class="btn btn-primary" href="<?=autoUrl("users/" . $id . "/rep/remove?squad=" . $squad['SquadID'] . "")?>">Remove</a>
              </span>
            </div>
          </div>
        </li>
        <?php } while ($squad = $getSquads->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
      <?php } else { ?>
      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            This user is not a rep for any squad
          </strong>
        </p>
      </div>
      <?php } ?>

      <p>
        <a href="<?=autoUrl("users/" . $id . "/rep/add")?>" class="btn btn-primary">
          Assign a squad
        </a>
      </p>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();