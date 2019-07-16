<?php

global $db;
global $systemInfo;

$pagetitle = "Swim England and Membership Fee Options";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
        echo $list->render('settings-fees');
      ?>
    </aside>
    <div class="col-md-9">
      <main>
        <h1>Swim England and Membership Fee Management</h1>
        <p class="lead">Set amounts for Swim England membership fees and club membership fees</p>

        <div class="list-group mb-3">
          <a href="<?=autoUrl("settings/fees/swim-england-fees")?>" class="list-group-item list-group-item-action">
            Swim England County, Regional and National Fees
          </a>
          <a href="<?=autoUrl("settings/fees/membership-fees")?>" class="list-group-item list-group-item-action">
            <?=htmlspecialchars(env('CLUB_NAME'))?> membership fees
          </a>
          <a href="<?=autoUrl("settings/fees/membership-discounts")?>" class="list-group-item list-group-item-action">
            Membership discounts by month
          </a>
          <a href="<?=autoUrl("settings/fees/charge-months")?>" class="list-group-item list-group-item-action">
            Months without squad fees
          </a>
        </div>
      </main>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';