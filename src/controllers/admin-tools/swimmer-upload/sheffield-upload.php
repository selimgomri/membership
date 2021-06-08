<?php

$pagetitle = 'Member Upload - University of Sheffield Version';

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("admin")) ?>">Admin</a></li>
      <li class="breadcrumb-item active" aria-current="page">Member upload (Uni of Sheffield)</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Member upload (for Uni of Sheffield only)</h1>
      <p class="lead">Batch upload members from a <span class="font-monospace">CSV</span> file.</p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['UploadSuccess']) && $_SESSION['TENANT-' . app()->tenant->getId()]['UploadSuccess']) { ?>
        <div class="alert alert-success">
          <p class="mb-0"><strong>Swimmers have been uploaded</strong>. Check below for any errors.</p>
        </div>
      <?php
        unset($_SESSION['TENANT-' . app()->tenant->getId()]['UploadSuccess']);
      } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['FailedSwimmers'])) { ?>
        <div class="alert alert-warning">
          <p>We've uploaded most of your swimmers but the following could not be uploaded as their squad could not be found;</p>
          <ul class="mb-0">
            <?php foreach ($_SESSION['TENANT-' . app()->tenant->getId()]['FailedSwimmers'] as $s) { ?>
              <li><?= htmlspecialchars($s) ?></li>
            <?php } ?>
          </ul>
        </div>
      <?php
        unset($_SESSION['TENANT-' . app()->tenant->getId()]['FailedSwimmers']);
      } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['UploadError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError']) { ?>
        <div class="alert alert-danger">
          <p class="mb-0"><strong>There was a problem with the file uploaded</strong>. Please try again.</p>
        </div>
      <?php
        unset($_SESSION['TENANT-' . app()->tenant->getId()]['UploadError']);
      } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['TooLargeError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['TooLargeError']) { ?>
        <div class="alert alert-danger">
          <p class="mb-0"><strong>The file you uploaded was too large</strong>. The maximum file size is 30000 bytes.</p>
        </div>
      <?php
        unset($_SESSION['TENANT-' . app()->tenant->getId()]['TooLargeError']);
      } ?>

      <p>
        Your CSV file should contain the following columns. We will assume the first three rows are headers.
      </p>

      <p>
        Columns marked <span class="badge bg-info">Disregarded</span> should be kept in the CSV so that you don't need to manipulate the layout, but the data in these columns will be ignored.
      </p>

      <p>
        We will check for existing members by checking if their email address is already in the system. Existing members will not be added again. For this to work, members must not change their email address from their <span class="font-monospace">@sheffield.ac.uk</span> email.
      </p>

      <ol class="font-monospace">
        <li>Empty column <span class="badge bg-info">Disregarded</span></li>
        <li>Member ID <span class="badge bg-info">Disregarded</span></li>
        <li>UCard Number <span class="badge bg-info">Disregarded</span></li>
        <li>Name - We will automatically explode this into first and last</li>
        <li>Date of birth (DD/MM/YYYY format)</li>
        <li>Age <span class="badge bg-info">Disregarded</span></li>
        <li>Gender/Sex as one of Male or Female</li>
        <li>Email Address (@sheffield.ac.uk)</li>
        <li>Subscription <span class="badge bg-info">Disregarded</span></li>
        <li>Empty column <span class="badge bg-info">Disregarded</span></li>
        <li>Subscription Start Date <span class="badge bg-info">Disregarded</span></li>
        <li>Subscription End Date <span class="badge bg-info">Disregarded</span></li>
      </ol>

      <form enctype="multipart/form-data" method="post" class="needs-validation" novalidate>

        <?= \SCDS\FormIdempotency::write() ?>
        <?= \SCDS\CSRF::write() ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="30000">

        <div class="mb-3">
          <label class="form-label" for="file-upload">Select a member file to upload</label>
          <input type="file" class="form-control" id="file-upload" name="file-upload" accept="text/csv" required>
          <div class="invalid-feedback">
            Please select a CSV file to upload
          </div>
        </div>

        <p>
          <button type="submit" class="btn btn-success">
            Upload
          </button>
        </p>

      </form>
    </div>

    <div class="col">
      <?php
      $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/admin-tools/list.json'));
      echo $list->render('admin-member-upload-uni-of-shef');
      ?>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJS("js/bs-custom-file-input.min.js");
$footer->addJS("js/file-input-init.js");
$footer->addJS("js/NeedsValidation.js");
$footer->render();
