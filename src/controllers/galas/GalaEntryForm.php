<?php

global $db;

$userID = $_SESSION['UserID'];
$pagetitle = "Enter a Gala";

$mySwimmers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM `members` WHERE `members`.`UserID` = ? ORDER BY fn ASC, sn ASC");
$mySwimmers->execute([$_SESSION['UserID']]);

$galas = $db->query("SELECT GalaID id, GalaName `name` FROM `galas` WHERE ClosingDate >= CURDATE() ORDER BY `galas`.`ClosingDate` ASC");

$mySwimmer = $mySwimmers->fetch(PDO::FETCH_ASSOC);
$gala = $galas->fetch(PDO::FETCH_ASSOC);

$hasSwimmer = true;
$hasGalas = true;
if ($mySwimmer == null) {
  $hasSwimmer = false;
}
if ($gala == null) {
  $hasGalas = false;
}

$use_white_background = true;

include BASE_PATH . "views/header.php";
include "galaMenu.php";
?>
<div class="container">
  <h1 class="mb-3">Enter a gala</h1>

  <?php if ($hasSwimmer && $hasGalas) { ?>
    <div>
      <form method="post">
      <h2>Select Swimmer and Gala</h2>
      <div class="form-group row">
        <label for="swimmer" class="col-sm-2 col-form-label">Select Swimmer</label>
        <div class="col-sm-10">
          <select class="custom-select" id="swimmer" name="swimmer" required><option value="null" selected>Select a swimmer</option>
          <?php do { ?>
            <option value="<?=$mySwimmer['id']?>">
              <?=htmlspecialchars($mySwimmer['fn'] . " " . $mySwimmer['sn'])?>
            </option>
          <?php } while ($mySwimmer = $mySwimmers->fetch(PDO::FETCH_ASSOC)); ?>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="gala" class="col-sm-2 col-form-label">Select Gala</label>
        <div class="col-sm-10">
          <select class="custom-select" id="gala" name="gala" required><option value="null" selected>Select a gala</option>
          <?php do { ?>
            <option value="<?=$gala['id']?>">
              <?=htmlspecialchars($gala['name'])?>
            </option>
          <?php } while ($gala = $galas->fetch(PDO::FETCH_ASSOC)); ?>
          </select>
        </div>
      </div>
      <div class="ajaxArea mb-3" id="output">
        <div class="ajaxPlaceholder">Select a swimmer and gala
        </div>
      </div>
      <p>
        <button type="submit" id="submit" class="btn btn-success">Submit</button>
      </p>
      </div>
    </form>
    <?php
  } else { ?>
    <p class="lead">
      We're unable to let you enter a gala at the moment
    </p>
    <p>
      This is because;
    </p>

    <?php if (!$hasSwimmer) { ?>
    <div class="alert alert-warning">
      <strong>You don't have any swimmers associated with your account</strong> <br>
      Please <a href="<?=autoUrl("my-account/addswimmer")?>" class="alert-link">add some swimmers in My Account</a>, then try again
    </div>
    <?php } ?>

    <?php if (!$hasGalas) { ?>
    <div class="alert alert-danger">
      <strong>There are no galas open for entries at the moment</strong>
    </div>
    <?php } ?>

  <?php } ?>
</div>

<script src="<?=autoUrl("js/gala-entry-form.js")?>"></script>

<?php include BASE_PATH . "views/footer.php";
