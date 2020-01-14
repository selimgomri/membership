<?php

$pagetitle = "SDIF Upload";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("admin"))?>">Admin</a></li>
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("admin/galas"))?>">Galas</a></li>
      <!-- <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("admin/galas/sdif"))?>">Admin</a></li> -->
      <li class="breadcrumb-item active" aria-current="page">Upload</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>SDIF Upload</h1>
      <p class="lead">Upload times for your members quickly using an SDIF (*.sd3) file.</p>

      <?php if (isset($_SESSION['UploadSuccess']) && $_SESSION['UploadSuccess']) { ?>
      <div class="alert alert-success">
        <p class="mb-0"><strong>Results have been uploaded</strong>.</p>
      </div>
      <?php
        unset($_SESSION['UploadSuccess']);
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
        <p class="mb-0"><strong>The file you uploaded was too large</strong>. The maximum file size is 300000 bytes.</p>
      </div>
      <?php
        unset($_SESSION['TooLargeError']);
      } ?>

      <p>This software will carry out checks to ensure it does not add duplicate records.</p>

      <form enctype="multipart/form-data" method="post">

        <?=\SCDS\FormIdempotency::write()?>
        <?=\SCDS\CSRF::write()?>
        <input type="hidden" name="MAX_FILE_SIZE" value="300000">

        <div class="form-group">
          <label>Select a result file to upload</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="file-upload" name="file-upload" accept="text/plain,.sd3">
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

<script src="<?=htmlspecialchars(autoUrl("public/js/bs-custom-file-input.min.js"))?>"></script>
<script src="<?=htmlspecialchars(autoUrl("public/js/file-input-init.js"))?>"></script>

<?php

include BASE_PATH . 'views/footer.php';