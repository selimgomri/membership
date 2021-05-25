<?php

$db = app()->db;
$tenant = app()->tenant;

$user = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$pagetitle = "Gala Payments";

$earliestEndDate = new DateTime('first day of last month', new DateTimeZone('Europe/London'));

$galas = $db->prepare("SELECT * FROM `galas` WHERE Tenant = ? AND `GalaDate` >= ?");
$galas->execute([
  $tenant->getId(),
  $earliestEndDate->format("Y-m-d")
]);
$gala = $galas->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

 ?>

<div class="container">
  <nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
			<li class="breadcrumb-item active" aria-current="page">Galas</li>
		</ol>
	</nav>

  <div class="row">
    <div class="col-md-8">
      <h1 class="">Payments for Galas</h1>
      <p class="lead">Charge users for gala entries</p>
      <hr>
      <?php if ($gala != null) { ?>
        <h2 class="mb-3">Galas to Charge or Refund</h2>
        <ul class="list-group">
          <?php do { ?>
          <li class="list-group-item list-group-item-action">
            <p class="mb-0">
              <strong>
                <a href="<?=autoUrl("galas/" . $gala['GalaID']); ?>">
                  <?=htmlspecialchars($gala['GalaName'])?>
                </a>
              </strong>
            </p>
            <p class="mb-0">
              <a href="<?=autoUrl("galas/" . $gala['GalaID'] . '/charges'); ?>">Charge for Entries</a> or <a href="<?=autoUrl("galas/" . $gala['GalaID'] . '/refunds'); ?>">Issue Refunds</a>
            </p>
          </li>
          <?php } while ($gala = $galas->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>
      <?php } else { ?>
        <div class="alert alert-warning">
          <strong>There are no galas open for charges</strong>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
