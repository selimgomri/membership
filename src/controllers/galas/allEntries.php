<?php

global $db;

$pagetitle = "Gala Entries";
$galaID = $surname = null;
$title = "View Gala Entries by Gala";

$use_white_background = true;

$galaIDParam = $search = $sex = "";
if (isset($_GET['gala'])) {
  $galaIDParam = intval($_GET['gala']);
}
if (isset($_GET['search'])) {
  $search = $_GET['search'];
}
if (isset($_GET['sex'])) {
  $sex = $_GET['sex'];
}

$galas = $db->query("SELECT GalaID, GalaName FROM `galas` WHERE GalaDate >= CURDATE() ORDER BY `galas`.`GalaDate` DESC");

include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Gala entries</li>
    </ol>
  </nav>
  <h1>Gala entries</h1>
  <p class="lead">Search entries for upcoming galas. Search by Gala or Gala and Surname.</p>

  <?php if (isset($_SESSION['Browser']['Name']) && ($_SESSION['Browser']['Internet Explorer'] || $_SESSION['Browser']['Name'] == 'Edge')) { ?>
    <div class="alert alert-warning">
      <p class="mb-0"><strong>We're aware of an issue affecting this page in Internet Explorer and Microsoft Edge.</strong></p>
      <p class="mb-0">We're investigating the issue. In the meantime, this page works as expected in other common browsers such as <a href="https://firefox.com" class="alert-link">Mozilla Firefox</a> and <a href="https://www.google.com/chrome/" class="alert-link">Google Chrome</a>.</p>
    </div>
  <?php } ?>

  <form id="entry-details" data-page-url="<?=htmlspecialchars(autoUrl("galas/entries"))?>" data-ajax-url="<?=htmlspecialchars(autoUrl("galas/ajax/entries"))?>" data-processed-url="<?=htmlspecialchars(autoUrl("galas/ajax/entryProcessed"))?>" class="">
    <div class="form-row d-print-none">
      <div class="col-md-4">
        <div class="form-group">
          <label class="" for="gala">Select a Gala</label>
          <select class="custom-select" placeholder="Select a Gala" id="galaID" name="galaID">
            <option>Select a gala</option>
            <option value="allGalas" <?php if ($galaIDParam == "allGalas") { ?> selected <?php } ?>>Show All Gala
              Entries</option>

            <?php while ($row = $galas->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?=$row['GalaID']?>" <?php if ($galaIDParam == $row['GalaID']) { ?> selected <?php } ?>>
              <?=htmlspecialchars($row['GalaName'])?>
            </option>
            <?php } ?>

          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label class="" for="sex">Select Sex</label>
          <select class="custom-select" placeholder="Select Sex" id="sex" name="sex">
            <option value="all" <?php if ($sex == "all") { ?> selected <?php } ?>>All Swimmers</option>
            <option value="f" <?php if ($sex == "f") { ?> selected <?php } ?>>
              Female
            </option>
            <option value="m" <?php if ($sex == "m") { ?> selected <?php } ?>>
              Male
            </option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <label for="gala">Enter Surname</label>
          <input class="form-control" placeholder="Search" name="search" id="search"
            value="<?=htmlspecialchars($search)?>">
        </div>
      </div>
    </div>

    <div class="table-responsive-md" id="output">
      <div class="ajaxPlaceholder">
        <strong>Select a Gala</strong><br>
        Entries will appear here when you select a gala
      </div>
    </div>
  </form>
</div>

<script src="<?=htmlspecialchars(autoUrl("public/js/gala-entries/ViewEntries.js"))?>"></script>
<?php include BASE_PATH . "views/footer.php";

?>