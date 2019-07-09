<?php

$fluidContainer = true;

$pagetitle = "System Settings";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 order-1 order-md-0">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
        echo $list->render('settings-home');
      ?>
    </aside>
    <div class="col-md-9 order-0 order-md-1">
      <main>
        <h1>System Settings</h1>
        <p class="lead">Manage system options</p>
      </main>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';