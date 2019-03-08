<?php

$pagetitle = "New Qualification";

$form = $_SESSION['NewQualificationData'];

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>New Qualification</h1>

  <div class="row">
    <div class="col-md-8">
      <p class="lead">
        Add a new qualification.
      </p>

      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="name">Qualification name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="DBS Qualification" value="<?=htmlspecialchars($form['name'])?>" required>
        </div>

        <button type="submit" class="btn btn-success">
          Add Qualification
        </button>
      </form>
    </div>
  </div>
</div>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';
