<?php


$db = app()->db;
$tenant = app()->tenant;

$codesOfConduct = $db->prepare("SELECT Title, ID FROM posts WHERE Tenant = ? AND`Type` = 'conduct_code' ORDER BY Title ASC");
$codesOfConduct->execute([
  $tenant->getId()
]);

$sql = $db->prepare("SELECT * FROM `squads` WHERE Tenant = ? AND squads.SquadID = ?");
$sql->execute([
  $tenant->getId(),
  $id
]);
$row = $sql->fetch(PDO::FETCH_ASSOC);

$title = $pagetitle = $row['SquadName'] . " Squad";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php"; ?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= autoUrl("squads") ?>">Squads</a></li>
      <li class="breadcrumb-item"><a href="<?= autoUrl("squads/" . $id) ?>"><?= htmlspecialchars($row['SquadName']) ?></a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  </nav>
  <h1><?= $title ?></h1>

  <div class="row">
    <div class="col-lg-8">
      <div class="">
        <form method="post">
          <h2>Details</h2>
          <p class="lead">View or edit the squad details</p>

          <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['UpdateDatabaseError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['UpdateDatabaseError']) { ?>
            <div class="alert alert-danger">A database error occured. We did not save the changes.</div>
          <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['UpdateDatabaseError']);
          } ?>

          <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['UpdateSuccess']) && $_SESSION['TENANT-' . app()->tenant->getId()]['UpdateSuccess']) { ?>
            <div class="alert alert-success">All changes saved</div>
          <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['UpdateSuccess']);
          } ?>

          <div class="form-group">
            <label for="squadName">Squad Name</label>
            <input type="text" class="form-control" id="squadName" name="squadName" placeholder="Enter Squad Name" value="<?= htmlspecialchars($row['SquadName']) ?>">
          </div>
          <div class="form-group">
            <label for="squadFee" class="form-label">Squad Fee</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">&pound;</span>
              </div>
              <input type="text" class="form-control" id="squadFee" name="squadFee" aria-describedby="squadFeeHelp" placeholder="eg 50.00" value="<?= htmlspecialchars($row['SquadFee']) ?>">
            </div>
            <small id="squadFeeHelp" class="form-text text-muted">A squad can have a fee of &pound;0.00 if it represents
              a group for non paying members</small>
          </div>
          <div class="form-group d-none">
            <label for="squadCoach">Squad Coach</label>
            <input type="text" class="form-control" id="squadCoach" name="squadCoach" placeholder="Enter Squad Coach" value="<?= htmlspecialchars($row['SquadCoach']) ?>">
          </div>
          <div class="form-group">
            <label for="squadTimetable">Squad Timetable</label>
            <input type="text" class="form-control" id="squadTimetable" name="squadTimetable" placeholder="Enter Squad Timetable Address" value="<?= htmlspecialchars($row['SquadTimetable']) ?>">
          </div>
          <div class="form-group">
            <label for="squadCoC">Squad Code of Conduct</label>
            <select class="custom-select" id="squadCoC" name="squadCoC" aria-describedby="conductSelectHelpBlock">
              <?php while ($codeDetails = $codesOfConduct->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?= htmlspecialchars($codeDetails['ID']) ?>" <?php if ($row['SquadCoC'] == $codeDetails['ID']) { ?>selected<?php } ?>>
                  <?= htmlspecialchars($codeDetails['Title']) ?>
                </option>
              <?php } ?>
            </select>
            <small id="conductSelectHelpBlock" class="form-text text-muted">
              You can create a code of conduct in the <strong>Posts</strong> section of this system and select it here.
              It will be used in various parts of this system, including when new members sign up and when members
              renew.
            </small>
          </div>
          <div class="alert alert-danger">
            <div class="form-group mb-0">
              <label for="squadDeleteDanger"><strong>Danger Zone</strong> <br>Delete this Squad with this Key "<span class="mono"><?= htmlspecialchars($row['SquadKey']) ?></span>"</label>
              <input type="text" class="form-control mono" id="squadDeleteDanger" name="squadDeleteDanger" aria-describedby="squadDeleteDangerHelp" placeholder="Enter the key" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
              <small id="squadDeleteDangerHelp" class="form-text">Enter the key in quotes above and press submit. This
                will delete this squad.</small>
            </div>
          </div>
          <p>
            <button class="btn btn-success" type="submit">Update</button>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();

?>