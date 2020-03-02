<?php

$pagetitle = "Upload Times";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>
        Upload Times
      </h1>

      <form method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label>Upload file for last 12 months</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="last-12" name="last-12">
            <label class="custom-file-label" for="last-12">Choose file</label>
          </div>
        </div>

        <div class="form-group">
          <label>Upload file for all time</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="all-time" name="all-time">
            <label class="custom-file-label" for="all-time">Choose file</label>
          </div>
        </div>

        <p>
          <button type="submit" class="btn btn-success">
            Upload times
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();