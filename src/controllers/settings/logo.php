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
      <form method="post">

        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">

        <div class="form-group">
          <label>Upload logo</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" accept="image/png,image/jpeg" id="file-upload" name="file-upload" multiple data-max-total-file-size="10485760" data-max-file-size="3145728" data-error-message-id="file-upload-invalid-feedback">
            <label class="custom-file-label text-truncate" for="file-upload">Choose image</label>
            <div class="invalid-feedback" id="file-upload-invalid-feedback">
              Oh no!
            </div>
          </div>
        </div>

        <p>
          <button class="btn btn-success" type="submit">
            Save
          </button>
        </p>
      </form>
    </main>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->addJS("public/js/notify/FileUpload.js");
$footer->render();