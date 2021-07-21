<?php

$db = app()->db;
$tenant = app()->tenant;
$getGala = $db->prepare("SELECT * FROM `galas` WHERE `GalaID` = ? AND Tenant = ?");
$getGala->execute([
  $id,
  $tenant->getId()
]);

$row = $getGala->fetch(PDO::FETCH_ASSOC);
$course = ['LONG', 'SHORT', 'IRREGULAR'];
$courseStrings = ['Long Course', 'Short Course', 'Other Pool Length or Open Water'];

if ($row == null) {
  halt(404);
}

$pagetitle = $row['GalaName'];
$title = $row['GalaName'];

include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("galas")) ?>">Galas</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("galas/" . $id)) ?>">#<?= htmlspecialchars($id) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

  </div>
</div>

<div class="container-xl">
  <div class="row">
    <div class="col-md-6">
      <form method="post">
        <div class="mb-3 row">
          <label class="form-label" for="galaname" class="col-sm-4 col-form-label">Gala Name</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="galaname" name="galaname" placeholder="<?= htmlspecialchars('e.g. ' . app()->tenant->getKey('CLUB_NAME') . ' Open Meet') ?>" value="<?= htmlspecialchars($row['GalaName']) ?>" required>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="form-label" for="description" class="col-sm-4 col-form-label">Description (optional)</label>
          <div class="col-sm-8">
            <textarea name="description" id="description" class="form-control font-monospace" aria-describedby="descriptionHelp"><?= htmlspecialchars($row['Description']) ?></textarea>
            <small id="descriptionHelp" class="form-text text-muted">
              A description is optional and will only be displayed if you enter something. Markdown is supported here.
            </small>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="form-label" for="length" class="col-sm-4 col-form-label">Course Length</label>
          <div class="col-sm-8">
            <select class="form-select" name="length" id="length" required>";
              <?php for ($i = 0; $i < sizeof($course); $i++) {
                if ($course[$i] == $row['CourseLength']) { ?>
                  <option selected value="<?= $course[$i] ?>">
                    <?= $courseStrings[$i] ?>
                  </option>
                <?php } else { ?>
                  <option value="<?= $course[$i] ?>">
                    <?= $courseStrings[$i] ?>
                  </option>
              <?php }
              } ?>
            </select>
          </div>
        </div>
        <div class="mb-3 row">
          <label class="form-label" for="venue" class="col-sm-4 col-form-label">Gala Venue</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="venue" name="venue" value="<?= htmlspecialchars($row['GalaVenue']) ?>" placeholder="<?= htmlspecialchars('e.g. ' . app()->tenant->getKey('CLUB_NAME') . ' Pool') ?>" required>
          </div>
        </div>
        <div class="mb-3 row">
          <label class="form-label" for="closingDate" class="col-sm-4 col-form-label">Closing Date</label>
          <div class="col-sm-8">
            <input type="date" class="form-control" id="closingDate" name="closingDate" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="<?= htmlspecialchars($row['ClosingDate']) ?>" required>
          </div>
        </div>
        <div class="mb-3 row">
          <label class="form-label" for="lastDate" class="col-sm-4 col-form-label">Last Day of Gala</label>
          <div class="col-sm-8">
            <input type="date" class="form-control" id="galaDate" name="galaDate" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="<?= htmlspecialchars($row['GalaDate']) ?>" required>
          </div>
        </div>

        <?php if ($row['HyTek'] == 1) { ?>
          <div class="mb-3 row">
            <label class="form-label" for="HyTek" class="col-sm-4 col-form-label">Require times?</label>
            <div class="col-sm-8">
              <div class="form-check mt-2">
                <input type="checkbox" value="1" class="form-check-input" checked id="HyTek" name="HyTek">
                <label class="form-check-label" for="HyTek">Tick if this is a HyTek gala or needs times from parents</label>
              </div>
            </div>
          </div>
        <?php } else { ?>
          <div class="mb-3 row">
            <label class="form-label" for="HyTek" class="col-sm-4 col-form-label">Require times?</label>
            <div class="col-sm-8">
              <div class="form-check mt-2">
                <input type="checkbox" value="1" class="form-check-input" id="HyTek" name="HyTek">
                <label class="form-check-label" for="HyTek">Tick if this is a HyTek gala or needs times from parents</label>
              </div>
            </div>
          </div>
        <?php } ?>

        <div class="mb-3 row">
          <label class="form-label" for="coachDecides" class="col-sm-4 col-form-label">Coach decides entries?</label>
          <div class="col-sm-8">
            <div class="form-check mt-2">
              <input type="checkbox" value="1" class="form-check-input" <?php if ($row['CoachEnters']) { ?>checked<?php } ?> id="coachDecides" name="coachDecides">
              <label class="form-check-label" for="coachDecides">Tick if a coach will make entries for this gala</label>
            </div>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="form-label" for="approvalNeeded" class="col-sm-4 col-form-label">Approval needed?</label>
          <div class="col-sm-8">
            <div class="form-check mt-2">
              <input type="checkbox" value="1" class="form-check-input" <?php if ($row['RequiresApproval']) { ?>checked<?php } ?> id="approvalNeeded" name="approvalNeeded">
              <label class="form-check-label" for="approvalNeeded">Tick if entries must first be approved by a squad rep. Entries are automatically approved if a squad does not have a squad rep.</label>
            </div>
          </div>
        </div>

        <p>
          <button class="btn btn-success" type="submit" id="submit">
            Save Changes
          </button>
        </p>
      </form>
    </div>

    <div class="col-md-6">
      <div class="cell">
        <h2>Entries</h2>
        <p class="mb-0">
          <a href="<?= autoUrl("galas/entries?galaID=" . $id . "&sex=all&search=") ?>" class="btn btn-dark-l btn-outline-light-d">
            View All Entries
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
