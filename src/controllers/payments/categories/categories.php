<?php

global $db;

$pagetitle = "Categories";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item active" aria-current="page">Categories</li>
    </ol>
  </nav>

  <h1>
    Categories
  </h1>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();