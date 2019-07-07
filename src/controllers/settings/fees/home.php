<?php

global $db;
global $systemInfo;

$pagetitle = "Swim England and Membership Fee Options";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <main class="col-lg-8">
      <h1>Swim England and Membership Fee Management</h1>
      <p class="lead">Set amounts for Swim England membership fees and club membership fees</p>

      <div class="list-group mb-3">
        <a href="<?=autoUrl("settings/fees/swim-england-fees")?>" class="list-group-item list-group-item-action">
          Swim England County, Regional and National Fees
        </a>
        <a href="<?=autoUrl("settings/fees/membership-fees")?>" class="list-group-item list-group-item-action">
          <?=htmlspecialchars(env('CLUB_NAME'))?> Membership Fees
        </a>
      </div>
    </main>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';