<?php

$db = app()->db;
$tenant = app()->tenant;
$pagetitle = 'Check Out - Contact Tracing';

$getLocations = $db->prepare("SELECT `ID`, `Name` FROM `covidLocations` WHERE `Tenant` = ? ORDER BY `Name` ASC");
$getLocations->execute([
  $tenant->getId(),
]);

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('contact-tracing')) ?>">Tracing</a></li>
        <li class="breadcrumb-item active" aria-current="page">Check Out</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Check Out
        </h1>
        <p class="lead mb-0">
          Check out those present at a location
        </p>
      </div>
    </div>

  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8">
      <?php if ($location = $getLocations->fetch(PDO::FETCH_ASSOC)) { ?>
        <div class="list-group">
          <?php do { ?>
            <a href="<?= htmlspecialchars(autoUrl('contact-tracing/check-out/' . $location['ID'])) ?>" class="list-group-item list-group-item-action">
              <?= htmlspecialchars($location['Name']) ?>
            </a>
          <?php } while ($location = $getLocations->fetch(PDO::FETCH_ASSOC)); ?>
        </div>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>No locations yet!</strong>
          </p>
          <p class="mb-0">
            Club staff can add a location so that you can get started.
          </p>
        </div>
      <?php } ?>
    </div>
  </div>
  
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();