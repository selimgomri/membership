<?php

$pagetitle = "Success - Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Success</h1>
      <p class="lead">
        We've created the account and added the swimmers.
      </p>

      <p>
        An email is on it's way to <?=htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegName'])?> which includes instructions to set a password.
      </p>
    </div>
  </div>
</div>

<?php

unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegName']);
unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUser']);
unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPass']);
unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegComplete']);

$footer = new \SCDS\Footer();
$footer->render();