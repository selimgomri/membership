<?php

$db = app()->db;

$pagetitle = "Database Migrations and Updates";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>Database Migrations</h1>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();