<?php

global $db;
$qual = $db->prepare("SELECT `Name` FROM qualificationsAvailable WHERE ID = ?");
$qual->execute([$id]);

$qualification = null;

$qualification = $qual->fetchColumn();

if ($qualification == null) {
  halt(404);
}

$pagetitle = "Edit " . htmlspecialchars($qualification);

$form = $_SESSION['EditQualificationData'];

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>Edit <?=htmlspecialchars($qualification)?></h1>

  <div class="row">
    <div class="col-md-8">

      <?php

      if ($_SESSION['EditQualificationSuccess']) {
        unset($_SESSION['EditQualificationSuccess']);
        ?>

        <div class="alert alert-success">
          <strong>Updated the Qualification</strong>
        </div>

      <?php } ?>

      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="name">Qualification name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="DBS Qualification" value="<?=htmlspecialchars($qualification)?>" required>
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
