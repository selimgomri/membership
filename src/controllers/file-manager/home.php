<?php

$pagetitle = "File Manager";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>File manager</h1>
      <p class="lead">Upload files to your membership system</p>

      <div class="list-group">
        <a href="<?=htmlspecialchars(autoUrl("file-manager/upload"))?>" class="list-group-item list-group-item-action">Upload files</a>
        <a href="<?=htmlspecialchars(autoUrl("file-manager/view"))?>" class="list-group-item list-group-item-action">View files</a>
      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SDCS\Footer();
$footer->render();