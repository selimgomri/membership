<?php

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>File upload</h1>

      <?php if (isset($_SESSION['UploadSuccess']) && $_SESSION['UploadSuccess']) { ?>
      <div class="alert alert-success">
        <p class="mb-0"><strong>Swimmers have been uploaded</strong>. Check below for any errors.</p>
      </div>
      <?php
        unset($_SESSION['UploadSuccess']);
      } ?>

      <?php if (isset($_SESSION['FailedSwimmers'])) { ?>
      <div class="alert alert-warning">
        <p>We've uploaded most of your swimmers but the following could not be uploaded as their squad could not be found;</p>
        <ul class="mb-0">
          <?php foreach ($_SESSION['FailedSwimmers'] as $s) { ?>
            <li><?=htmlspecialchars($s)?></li>
          <?php } ?>
        </ul>
      </div>
      <?php
        unset($_SESSION['FailedSwimmers']);
      } ?>

      <?php if (isset($_SESSION['UploadError']) && $_SESSION['UploadError']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0"><strong>There was a problem with the file uploaded</strong>. Please try again.</p>
      </div>
      <?php
        unset($_SESSION['UploadError']);
      } ?>

      <?php if (isset($_SESSION['TooLargeError']) && $_SESSION['TooLargeError']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0"><strong>The file you uploaded was too large</strong>. The maximum file size is 5 megabytes.</p>
      </div>
      <?php
        unset($_SESSION['TooLargeError']);
      } ?>

      <p>Some filetypes will not be accepted</p>

      <form enctype="multipart/form-data" method="post">

        <?=\SCDS\FormIdempotency::write()?>
        <?=\SCDS\CSRF::write()?>
        <input type="hidden" name="MAX_FILE_SIZE" value="5000000">

        <div class="form-group">
          <label>Select a file to upload</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="file-upload" name="file-upload" accept="audio/*,video/*,image/*,text/*,application/*">
            <label class="custom-file-label" for="file-upload">Choose file</label>
          </div>
        </div>

        <div class="form-group">
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="publicly-viewable-yes" name="publicly-viewable" class="custom-control-input">
            <label class="custom-control-label" for="publicly-viewable-yes">Anyone with the link can view</label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="publicly-viewable-no" name="publicly-viewable" class="custom-control-input" checked>
            <label class="custom-control-label" for="publicly-viewable-no">Members only can view</label>
          </div>
        </div>

        <p>
          <button type="submit" class="btn btn-success">
            Upload
          </button>
        </p>

      </form>
    </div>
  </div>
</div>

<script src="<?=htmlspecialchars(autoUrl("public/js/bs-custom-file-input.min.js"))?>"></script>
<script src="<?=htmlspecialchars(autoUrl("public/js/file-input-init.js"))?>"></script>

<?php

include BASE_PATH . 'views/footer.php';