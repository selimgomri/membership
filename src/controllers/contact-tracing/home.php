<?php

$db = app()->db;
$tenant = app()->tenant;
$pagetitle = 'Contact Tracing';

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Tracing</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Contact Tracing
        </h1>
        <p class="lead mb-0">
          <?= htmlspecialchars($tenant->getName()) ?> <em>Supporting NHS Test and Trace</em>
        </p>
        <div class="mb-3 d-lg-none"></div>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-md-6">
      <div class="p-3 bg-danger rounded text-white h-100">
        <div>
          <h2>
            Register your attendance
          </h2>
          <p class="lead">
            Together, we can help the NHS.
          </p>
          <p>
            <?= htmlspecialchars($tenant->getName()) ?> will use this data if required to support NHS Test and Trace. Data will be deleted automatically after 21 days.
          </p>
        </div>
        <p class="mb-0 mt-auto d-flex">
          <a href="<?= htmlspecialchars(autoUrl('contact-tracing/check-in')) ?>" class="btn btn-outline-light">
            Register
          </a>
        </p>
      </div>
    </div>
    <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') { ?>
      <div class="col-md-6">
        <div class="p-3 bg-dark rounded text-white h-100" style="display: grid;">
          <div>
            <h2>
              Manage locations and generate reports
            </h2>
          </div>
          <p class="mb-0 mt-auto d-flex">
            <a href="<?= htmlspecialchars(autoUrl('contact-tracing/locations')) ?>" class="btn btn-light">
              Manage
            </a>
          </p>
        </div>
      </div>
    <?php } ?>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
