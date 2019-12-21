<?php

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Swimmer upload</h1>
      <p class="lead">Batch upload swimmers from a <span class="mono">CSV</span> file.</p>

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
        <p class="mb-0"><strong>The file you uploaded was too large</strong>. The maximum file size is 30000 bytes.</p>
      </div>
      <?php
        unset($_SESSION['TooLargeError']);
      } ?>

      <p>Your CSV file should contain the following columns in the order shown without any column headers (titles etc);</p>

      <ul class="mono">
        <li>Last name</li>
        <li>First name</li>
        <li>Squad name</li>
        <li>Date of birth (DD/MM/YYYY format)</li>
        <li>Sex (M or F)</li>
        <li>Swim England Membership Category (1, 2 or 3 - use 0 or null if not a Swim England member)</li>
        <li>Swim England Membership Number, null if not a member or no number available</li>
      </ul>

      <p>Note that the system will attempt to find a squad with the name given. If it cannot find one, it will not add the swimmer.</p>

      <form enctype="multipart/form-data" method="post">

        <?=\SCDS\FormIdempotency::write()?>
        <?=\SCDS\CSRF::write()?>
        <input type="hidden" name="MAX_FILE_SIZE" value="30000">

        <div class="form-group">
          <label>Select a swimmer file to upload</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="file-upload" name="file-upload">
            <label class="custom-file-label" for="file-upload">Choose file</label>
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

<?php

include BASE_PATH . 'views/footer.php';