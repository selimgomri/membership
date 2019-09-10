<?php

global $db;
$getGala = $db->prepare("SELECT * FROM `galas` WHERE `GalaID` = ?");
$getGala->execute([$id]);

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

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit <?=htmlspecialchars($row['GalaName'])?></li>
    </ol>
  </nav>
  <div class="row">
    <div class="col-md-6">
      <form method="post">
        <div class="form-group row">
          <label for="galaname" class="col-sm-4 col-form-label">Gala Name</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="galaname" name="galaname" placeholder="eg Chester-le-Street Open" value="<?=htmlspecialchars($row['GalaName'])?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="length" class="col-sm-4 col-form-label">Course Length</label>
          <div class="col-sm-8">
            <select class="custom-select" name="length" id="length" required>";
            <?php for ($i=0; $i<sizeof($course); $i++) {
              if ($course[$i] == $row['CourseLength']) { ?>
                <option selected value="<?=$course[$i]?>">
                  <?=$courseStrings[$i]?>
                </option>
              <?php } else { ?>
                <option value="<?=$course[$i]?>">
                  <?=$courseStrings[$i]?>
                </option>
              <?php }
            } ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="venue" class="col-sm-4 col-form-label">Gala Venue</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="venue" name="venue" value="<?=htmlspecialchars($row['GalaVenue'])?>" placeholder="eg Chester-le-Street" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="closingDate" class="col-sm-4 col-form-label">Closing Date</label>
          <div class="col-sm-8">
            <input type="date" class="form-control" id="closingDate" name="closingDate" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="<?=htmlspecialchars($row['ClosingDate'])?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="lastDate" class="col-sm-4 col-form-label">Last Day of Gala</label>
          <div class="col-sm-8">
            <input type="date" class="form-control" id="galaDate" name="galaDate" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="<?=htmlspecialchars($row['GalaDate'])?>" required>
          </div>
        </div>
        <?php if ($row['GalaFeeConstant'] == 1) { ?>
        <div class="form-group row">
          <label for="GalaFeeConstant" class="col-sm-4 col-form-label">Gala Fee Constant?</label>
          <div class="col-sm-8">
            <div class="custom-control custom-checkbox mt-2">
              <input type="checkbox" value="1" class="custom-control-input" checked id="GalaFeeConstant" name="GalaFeeConstant">
              <label class="custom-control-label" for="GalaFeeConstant">Tick if all swims are the same price</label>
            </div>
          </div>
        </div>
        <?php } else { ?>
        <div class="form-group row">
          <label for="GalaFeeConstant" class="col-sm-4 col-form-label">Gala Fee Constant?</label>
          <div class="col-sm-8">
            <div class="custom-control custom-checkbox mt-2">
              <input type="checkbox" value="1" class="custom-control-input" id="GalaFeeConstant" name="GalaFeeConstant">
              <label class="custom-control-label" for="GalaFeeConstant">Tick if all swims are the same price</label>
            </div>
          </div>
        </div>
        <?php } ?>

        <div class="form-group row">
          <label for="galaFee" class="col-sm-4 col-form-label">Gala Fee</label>
          <div class="col-sm-8">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">&pound;</span>
              </div>
              <input type="text" class="form-control" id="galaFee" name="galaFee" aria-describedby="galaFeeHelp" placeholder="eg 5.00" value="<?=htmlspecialchars($row['GalaFee'])?>">
            </div>
            <small id="galaFeeHelp" class="form-text text-muted">If all swims at the gala are the same price, enter it here.</small>
          </div>
        </div>
        <?php if ($row['HyTek'] == 1) { ?>
        <div class="form-group row">
          <label for="HyTek" class="col-sm-4 col-form-label">Require times?</label>
          <div class="col-sm-8">
            <div class="custom-control custom-checkbox mt-2">
              <input type="checkbox" value="1" class="custom-control-input" checked id="HyTek" name="HyTek">
              <label class="custom-control-label" for="HyTek">Tick if this is a HyTek gala or needs times from parents</label>
            </div>
          </div>
        </div>
        <?php } else { ?>
        <div class="form-group row">
          <label for="HyTek" class="col-sm-4 col-form-label">Require times?</label>
          <div class="col-sm-8">
            <div class="custom-control custom-checkbox mt-2">
              <input type="checkbox" value="1" class="custom-control-input" id="HyTek" name="HyTek">
              <label class="custom-control-label" for="HyTek">Tick if this is a HyTek gala or needs times from parents</label>
            </div>
          </div>
        </div>
        <?php } ?>

        <div class="form-group row">
          <label for="coachDecides" class="col-sm-4 col-form-label">Coach decides entries?</label>
          <div class="col-sm-8">
            <div class="custom-control custom-checkbox mt-2">
        <input type="checkbox" value="1" class="custom-control-input" <?php if ($row['CoachEnters']) { ?>checked<?php } ?> id="coachDecides" name="coachDecides">
              <label class="custom-control-label" for="coachDecides">Tick if a coach will make entries for this gala</label>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <label for="approvalNeeded" class="col-sm-4 col-form-label">Approval needed?</label>
          <div class="col-sm-8">
            <div class="custom-control custom-checkbox mt-2">
        <input type="checkbox" value="1" class="custom-control-input" <?php if ($row['RequiresApproval']) { ?>checked<?php } ?> id="approvalNeeded" name="approvalNeeded">
              <label class="custom-control-label" for="approvalNeeded">Tick if entries must first be approved by a squad rep. Entries are automatically approved if a squad does not have a squad rep.</label>
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
          <a href="<?=autoUrl("galas/entries?galaID=" . $id . "&sex=all&search=")?>" class="btn btn-dark">
            View All Entries
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";