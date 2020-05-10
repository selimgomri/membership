<?php

$pagetitle = "Membership Software by Swimming Club Data Systems";

include BASE_PATH . "views/root/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>SCDS Membership</h1>
      <p class="lead">Welcome to our software.</p>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\RootFooter();
$footer->render(); ?>