<?php

$db = app()->db;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$userInfo->execute([$id]);
$info = $userInfo->fetch(PDO::FETCH_ASSOC);

$date = new DateTime('-1 day', new DateTimeZone('Europe/London'));
$getGalas = $db->prepare("SELECT GalaName, GalaID FROM teamManagers INNER JOIN galas ON galas.GalaID = teamManagers.Gala WHERE teamManagers.User = ? AND galas.GalaDate >= ?");
$getGalas->execute([
  $id,
  $date->format("Y-m-d")
]);
$gala = $getGalas->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($info['Forename'] . ' ' . $info['Surname']) . ' Team Manager Options';

include BASE_PATH . "views/header.php";

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("users")?>">Users</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("users/" . $id)?>"><?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?></a></li>
      <li class="breadcrumb-item active" aria-current="page">TM Settings</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>
        <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?><br><small>Team Manager Settings</small>
      </h1>

      <?php if (isset($_SESSION['AssignGalaSuccess']) && $_SESSION['AssignGalaSuccess']) { ?>
      <div class="alert alert-success">
        <p class="mb-0">
          <strong>
            We've assigned that gala to <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['AssignGalaSuccess']);
      } ?>

      <?php if (isset($_SESSION['RemoveGalaSuccess']) && $_SESSION['RemoveGalaSuccess']) { ?>
      <div class="alert alert-success">
        <p class="mb-0">
          <strong>
            We've removed that gala from <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['RemoveGalaSuccess']);
      } ?>

      <?php if (isset($_SESSION['RemoveGalaError']) && $_SESSION['RemoveGalaError']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>
            We could not remove that gala from <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['RemoveGalaError']);
      } ?>

      <?php if ($gala != null) { ?>
      <p>
        <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?> is a team manager for the following upcoming galas.
      </p>
      <ul class="list-group mb-3">
        <?php do { ?>
        <li class="list-group-item">
          <div class="row align-items-center justify-content-between">
            <div class="col">
              <?=htmlspecialchars($gala['GalaName'])?>
            </div>
            <div class="col text-right">
              <span>
                <a class="btn btn-primary" href="<?=autoUrl("users/" . $id . "/team-manager/remove?gala=" . $gala['GalaID'] . "")?>">Remove</a>
              </span>
            </div>
          </div>
        </li>
        <?php } while ($gala = $getGalas->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
      <p>
        There is no need to remove a team manager after a gala. They will automatically no longer have access to any information for a gala after it finishes.
      </p>
      <?php } else { ?>
      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?> is not a team manager for any future galas
          </strong>
        </p>
      </div>
      <?php } ?>

      <p>
        <a href="<?=autoUrl("users/" . $id . "/team-manager/add")?>" class="btn btn-primary">
          Assign a gala
        </a>
      </p>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();