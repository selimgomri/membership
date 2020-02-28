<?php

$pagetitle = "Admin Tools";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Admin</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Admin tools</h1>
      <p class="lead">Welcome to the admin tools dashboard.</p>

      <p>Over time we'll slowly be introducing new administrative tools to this dashboard including reports, settings and more.</p>

      <p>Only admin users will be able to access the reports available here and improved navigation will be introduced in due course.</p>

    </div>

    <div class="col">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/admin-tools/list.json'));
        echo $list->render('admin-home');
      ?>
    </div>
  </div>
</div>

<?php

$footer = new \SDCS\Footer();
$footer->render();