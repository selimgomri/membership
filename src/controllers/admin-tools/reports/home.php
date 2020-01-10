<?php

$pagetitle = "Admin Reports";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("admin"))?>">Admin</a></li>
      <li class="breadcrumb-item active" aria-current="page">Reports</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Admin reports</h1>
      <p class="lead">Welcome to the admin reports dashboard.</p>

      <ul>
        <li><a download href="<?=htmlspecialchars(autoUrl("admin/reports/membership-data-export.csv"))?>">Membership report (CSV download)</a></li>
      </ul>

    </div>

    <div class="col">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/admin-tools/list.json'));
        echo $list->render('admin-reports');
      ?>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';