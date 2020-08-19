<?php

$db = app()->db;
$tenant = app()->tenant;

$squads = $db->prepare("SELECT SquadID id, SquadName name FROM `squads` WHERE Tenant = ? ORDER BY `SquadFee` DESC, `SquadName` ASC");
$squads->execute([
	$tenant->getId()
]);
$squad = $squads->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Attendance History by Squad";
$title = "Attendance History by Squad";

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('attendance')) ?>">Attendance</a></li>
				<li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('attendance/history')) ?>">History</a></li>
        <li class="breadcrumb-item active" aria-current="page">Squads</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col">
        <h1>
					Attendance history by squad
        </h1>
        <p class="lead mb-0">
					View history for a squad
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">
	<div>

    <div class="row">
      <div class="col-md-8">

        <?php if ($squad == null) { ?>
        <div class="alert alert-warning">
          <strong>There are no squads to view</strong>
        </div>
        <?php } ?>

    		<div class="list-group">
    			<?php do { ?>
    			<a class="list-group-item list-group-item-action" href="<?=autoUrl("attendance/history/squads/" . $squad['id'])?>">
    				<span class="text-primary"><?=htmlspecialchars($squad['name'])?></span>
    			</a>
    			<?php } while ($squad = $squads->fetch(PDO::FETCH_ASSOC)); ?>
    		</div>

      </div>
    </div>

	</div>
</div>
<?php $footer = new \SCDS\Footer();
$footer->render();
