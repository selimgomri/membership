<?php

$db = app()->db;

$fluidContainer = true;

$pagetitle = "Club Membership Fee Options (V2)";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
      $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
      echo $list->render('settings-fees');
      ?>
    </aside>
    <div class="col-md-9">
      <main>
        <h1>New Club Membership Fee Class</h1>
        <p class="lead">Set amounts for club membership fees</p>

        <form method="post" class="needs-validation" novalidate>

          <div class="form-group">
            <label for="class-name">Class Name</label>
            <input type="text" name="class-name" id="class-name" class="form-control" required>
            <div class="invalid-feedback">
              Please provide a name for this type of membership
            </div>
          </div>

          <div class="form-group">
            <label for="class-description">Description (optional)</label>
            <textarea class="form-control" name="class-description" id="class-description" rows="5"></textarea>
          </div>

          <p>
            We'll set the fees for this class on the next page.
          </p>

          <?= \SCDS\CSRF::write(); ?>

          <p>
            <button type="submit" class="btn btn-success">Add class</button>
          </p>

        </form>

      </main>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
