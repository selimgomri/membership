<?php

$fluidContainer = true;

$db = app()->db;
$tenant = app()->tenant;

$pagetitle = "Club Logo";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
      $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
      echo $list->render('settings-logo');
      ?>
    </aside>
    <main class="col-md-9">
      <h1>Upload club logo</h1>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LOGO-SAVED']) && $_SESSION['TENANT-' . app()->tenant->getId()]['LOGO-SAVED']) { ?>
        <div class="alert alert-success">New logo saved.</div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['LOGO-SAVED']); } ?>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LOGO-ERROR']) && $_SESSION['TENANT-' . app()->tenant->getId()]['LOGO-ERROR']) { ?>
        <div class="alert alert-danger">Your new logo could not be saved.</div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['LOGO-ERROR']); } ?>

      <?php if ($logos = $tenant->getKey('LOGO_DIR')) { ?>
        <p>You currently have the below image set as your logo:</p>
        <div class="card card-body mb-3">
          <img src="<?= htmlspecialchars(autoUrl($logos . 'logo-150.png')) ?>" srcset="<?= htmlspecialchars(autoUrl($logos . 'logo-150@2x.png')) ?> 2x, <?= htmlspecialchars(autoUrl($logos . 'logo-150@3x.png')) ?> 3x" alt="<?= htmlspecialchars($tenant->getName()) ?> logo" class="img-fluid mx-auto">
        </div>
      <?php } ?>

      <form method="post" id="logo-form" enctype="multipart/form-data">

        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">

        <div class="form-group">
          <label>Upload logo</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" accept="image/png,image/jpeg" id="file-upload" name="file-upload" data-max-total-file-size="10485760" data-max-file-size="3145728" data-error-message-id="file-upload-invalid-feedback">
            <label class="custom-file-label text-truncate" for="file-upload">Choose image</label>
            <div class="invalid-feedback" id="file-upload-invalid-feedback">
              Oh no!
            </div>
          </div>
        </div>

        <p>
          We will convert your logo into multiple formats and store a master copy so that we can create versions of the logo at different sizes and file formats at a later date.
        </p>

        <p>
          It may take a few seconds for us to process your logo. Please wait patiently.
        </p>

        <p>
          <button class="btn btn-success" type="submit">
            Upload
          </button>
        </p>
      </form>
    </main>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->addJS("public/js/settings/logo-upload.js");
$footer->render();
