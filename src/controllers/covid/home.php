<?php

$db = app()->db;
$tenant = app()->tenant;
$pagetitle = 'COVID Tools';

// Show if this user is a squad rep
$getRepCount = $db->prepare("SELECT COUNT(*) FROM squadReps WHERE User = ?");
$getRepCount->execute([
  $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
]);
$showSignOut = $getRepCount->fetchColumn() > 0;

$user = app()->user;
if ($user->hasPermission('Admin') || $user->hasPermission('Coach') || $user->hasPermission('Galas')) {
  $showSignOut = true;
}

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">COVID</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          COVID-19 Tools
        </h1>
        <p class="lead mb-0">
          A number of COVID-19 tools are available to help clubs through the current situation.
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row">

    <div class="col-md-4">
      <div class="card card-body h-100" style="display: grid;">
        <div>
          <h2>
            Contact Tracing
          </h2>
          <p class="lead">
            We're keeping a record of those attending sessions.
          </p>
          <p>
            <?= htmlspecialchars($tenant->getName()) ?> can use your contact data (if required) to support NHS Test and Trace.
          </p>
        </div>
        <p class="mb-0 mt-auto d-flex">
          <a href="<?= htmlspecialchars(autoUrl('covid/contact-tracing')) ?>" class="btn btn-primary">
            Go
          </a>
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-body h-100" style="display: grid;">
        <div>
          <h2>
            Health Screening Survey
          </h2>
          <p class="lead">
            Swim England are recommending that all clubs carry out a periodic screening survey of all members who are training.
          </p>
          <p>
            Taking the screening survey helps keep yourself and other club members safe.
          </p>
        </div>
        <p class="mb-0 mt-auto d-flex">
          <a href="<?= htmlspecialchars(autoUrl('covid/health-screening')) ?>" class="btn btn-primary">
            Go
          </a>
        </p>
      </div>
    </div>

    <!-- <div class="col-md-4">
      <div class="card card-body h-100" style="display: grid;">
        <div>
          <h2>
            Risk Awareness Declaration (WIP)
          </h2>
        </div>
        <p class="mb-0 mt-auto d-flex">
          <a href="<?= htmlspecialchars(autoUrl('covid/risk-awareness')) ?>" class="btn btn-primary">
            Go
          </a>
        </p>
      </div>
    </div> -->

  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
