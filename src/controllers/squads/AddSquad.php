<?php
$title = $pagetitle = "Add a Squad";

$db = app()->db;
$tenant = app()->tenant;

$codesOfConduct = $db->prepare("SELECT Title, ID FROM posts WHERE Tenant = ? AND `Type` = 'conduct_code' ORDER BY Title ASC");
$codesOfConduct->execute([
  $tenant->getId()
]);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php"; ?>

<div class="container">
  <h1>Add a squad</h1>

  <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['SquadAddError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['SquadAddError']) { ?>
    <div class="alert alert-danger">
      <p class="mb-0">
        <strong>There was a problem trying to add your new squad</strong>
      </p>
      <p class="mb-0">
        <?= htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['SquadAddError']['message']) ?>
      </p>
    </div>
  <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['SquadAddError']);
  } ?>

  <div class="">
    <h2 class="">Squad Details</h2>
    <form method="post" action="<?= htmlspecialchars(autoUrl("squads/addsquad")) ?>" class="needs-validation" novalidate>

      <div class="form-group">
        <label for="squadName">Squad Name</label>
        <input type="text" class="form-control" id="squadName" name="squadName" placeholder="Enter Squad Name" required>
        <div class="invalid-feedback">
          Please provide a squad name.
        </div>
      </div>
      <div class="form-group">
        <label for="squadFee" class="form-label">Squad Fee</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">&pound;</span>
          </div>
          <input type="number" min="0" step="0.01" class="form-control" id="squadFee" name="squadFee" aria-describedby="squadFeeHelp" placeholder="eg 50.00" required>
          <div class="invalid-feedback">
            Please provide a monthly fee.
          </div>
        </div>
        <small id="squadFeeHelp" class="form-text text-muted">A squad can have a fee of &pound;0.00 if it represents a group for non paying members</small>
      </div>
      <div class="form-group d-none">
        <label for="squadCoach">Squad Coach</label>
        <input type="text" class="form-control" id="squadCoach" name="squadCoach" placeholder="Enter Squad Coach">
      </div>
      <div class="form-group">
        <label for="squadTimetable">Squad Timetable</label>
        <input type="url" class="form-control" id="squadTimetable" name="squadTimetable" placeholder="Enter Squad Timetable Address">
      </div>
      <div class="form-group">
        <label for="squadCoC">Squad Code of Conduct</label>
        <select class="custom-select" id="squadCoC" name="squadCoC" aria-describedby="conductSelectHelpBlock">
          <option selected>Choose...</option>
          <?php while ($row = $codesOfConduct->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?= htmlspecialchars($row['ID']) ?>"><?= htmlspecialchars($row['Title']) ?></option>
          <?php } ?>
        </select>
        <small id="conductSelectHelpBlock" class="form-text text-muted">
          You can create a code of conduct in the <strong>Posts</strong> section of this system and select it here. It will be used in various parts of this system, including when new members sign up and when members renew.
        </small>
      </div>

      <p class="mb-0"><button class="btn btn-success" type="submit">Add Squad</button></p>

    </form>
  </div>

</div>
<?php $footer = new \SCDS\Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
?>