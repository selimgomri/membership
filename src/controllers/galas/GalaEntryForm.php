<?php

global $db;

$userID = $_SESSION['UserID'];
$pagetitle = "Enter a Gala";

$swimmerCount = 0;
if ($_SESSION['AccessLevel'] == 'Parent') {
  $count = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ?");
  $count->execute([$_SESSION['UserID']]);
  $swimmerCount = $count->fetchColumn();
} else if (isset($swimmer)) {
  $count = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`MemberID` = ?");
  $count->execute([$swimmer]);
  $swimmerCount = $count->fetchColumn();
}

$mySwimmers = null;
if ($_SESSION['AccessLevel'] == 'Parent') {
  $mySwimmers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM `members` WHERE `members`.`UserID` = ? ORDER BY fn ASC, sn ASC");
  $mySwimmers->execute([$_SESSION['UserID']]);
} else if (isset($swimmer)) {
  $mySwimmers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM `members` WHERE `members`.`MemberID` = ?");
  $mySwimmers->execute([$swimmer]);
}

$now = new DateTime('now', new DateTimeZone('Europe/London'));
$nowDay = $now->format('Y-m-d');
$galas = $db->prepare("SELECT GalaID id, GalaName `name` FROM `galas` WHERE ClosingDate >= ? ORDER BY `galas`.`ClosingDate` ASC");
$galas->execute([$nowDay]);

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

include BASE_PATH . "views/header.php";
include "galaMenu.php";
?>
<div class="container">

  <?php if ($_SESSION['AccessLevel'] != 'Parent') { ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("members")?>">Swimmers</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("members/" . $swimmer)?>"><?=htmlspecialchars($mySwimmer['fn'])?> <?=htmlspecialchars(mb_substr($mySwimmer['sn'], 0, 1, 'utf-8'))?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Enter a gala</li>
    </ol>
  </nav>
  <?php } else { ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("galas"))?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Enter gala</li>
    </ol>
  </nav>
  <?php } ?>

  <div id="gala-data" data-ajax-url="<?=htmlspecialchars(autoUrl('galas/ajax/entryForm'))?>"></div>

  <div class="row align-items-center">
    <div class="col-md-8">
      <h1 class="mb-3">Enter a gala</h1>
    </div>
    <div class="col text-md-right">
      <aside>
        <a href="<?=htmlspecialchars(autoUrl("galas/entergala/help"))?>" class="btn btn-info">
          Gala entry help <i class="fa fa-info-circle" aria-hidden="true"></i>
        </a>
        <div class="d-md-none mb-3"></div>
      </aside>
    </div>
  </div>

  <?php if ($hasSwimmer && $hasGalas) { ?>
    <div>
      <form method="post">
      <h2>Select Swimmer and Gala</h2>
      <div class="form-group row">
        <label for="swimmer" class="col-sm-2 col-form-label">Select Swimmer</label>
        <div class="col-sm-10">
          <select class="custom-select" id="swimmer" name="swimmer" required><option value="null" <?php if ($swimmerCount > 1) { ?>selected<?php } ?>>Select a swimmer</option>
          <?php do { ?>
            <option value="<?=$mySwimmer['id']?>" <?php if ($swimmerCount == 1) { ?>selected<?php } ?>>
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
        <button type="submit" id="submit" disabled class="btn btn-success">Submit</button>
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

<?php $footer = new \SCDS\Footer();
$footer->addJs("public/js/numerical/bignumber.min.js");
$footer->addJs("public/js/gala-entries/NewEntry.js");
$footer->render();
