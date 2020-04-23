<?php

$db = app()->db;
$getQualification = $db->prepare("SELECT COUNT(*) FROM qualifications WHERE ID = ?");
$getQualification->execute([$id]);

if ($getQualification->fetchColumn() == 0) {
  halt(404);
}

$getQualification = $db->prepare("SELECT `Name`, Info, `From`, `To`, Forename, Surname FROM qualifications INNER JOIN users ON qualifications.UserID = users.UserID WHERE ID = ?");
$getQualification->execute([$id]);

$qualification = $getQualification->fetch(PDO::FETCH_ASSOC);

$name = $qualification['Forename'] . ' ' . $qualification['Surname'];

$pagetitle = "Edit " . $qualification['Name'];

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>Edit <?=htmlspecialchars($qualification['Name'])?></h1>

  <div class="row">
    <div class="col-md-8">
      <p class="lead">
        Edit qualification for <?=htmlspecialchars($qualification['Forename'] . ' ' . $qualification['Surname'])?>.
      </p>

      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="name">Qualification name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="DBS Qualification" value="<?=htmlspecialchars($qualification['Name'])?>" required>
        </div>

        <div class="form-group">
          <label for="info">Qualification information (Optional)</label>
          <textarea class="form-control" id="info" name="info"><?=htmlspecialchars($qualification['Name'])?></textarea>
        </div>

        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="valid-from">Valid From</label>
              <input type="date" class="form-control" id="valid-from" name="valid-from" value="<?=htmlspecialchars($qualification['From'])?>" required>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <label class="custom-control-label" for="expires">Qualification Expires</label>
                <input type="checkbox" class="custom-control-input" id="expires" name="expires" value="1">
              </div>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <label for="valid-to">Valid To (Optional, unless Expires)</label>
              <input type="date" class="form-control" id="valid-to" name="valid-to" value="<?=htmlspecialchars($qualification['To'])?>">
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-success">
          Save Changes
        </button>
      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
