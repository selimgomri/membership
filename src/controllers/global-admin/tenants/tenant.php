<?php

$db = app()->db;
$getClubs = $db->prepare("SELECT `ID`, `Name`, `Code`, `Verified` FROM tenants WHERE `UniqueID` = ? ORDER BY `Name` ASC");
$getClubs->execute([
  $id
]);
$club = $getClubs->fetch(PDO::FETCH_ASSOC);

if (!$club) halt(404);

$tenant = Tenant::fromId($club['ID']);

$pagetitle = "Information - " . htmlspecialchars($tenant->getName());

include BASE_PATH . "views/root/header.php";

?>

<div class="container">
  <div class="row justify-content-center py-3">
    <div class="col-lg-8 col-md-10">
      <div class="bg-primary text-white p-4 mb-4 d-inline-block rounded">
        <h1 class="mb-0"><?= htmlspecialchars($club['Name']) ?><?php if (bool($club['Verified'])) { ?> <i class="fa fa-check-circle text-white" aria-hidden="true"></i><?php } ?></h1>
        <!-- <p class="mb-0">Find your club to get started.</p> -->
      </div>

      <h2>Information</h2>

      <dl class="row">
        <dt class="col-lg-4">
          Name
        </dt>
        <dd class="col-lg-8">
          <?= htmlspecialchars($tenant->getName()) ?>
        </dd>

        <dt class="col-lg-4">
          UUID
        </dt>
        <dd class="col-lg-8 font-monospace">
          <?= htmlspecialchars($tenant->getUuid()) ?>
        </dd>

        <dt class="col-lg-4">
          ID
        </dt>
        <dd class="col-lg-8 font-monospace">
          <?= htmlspecialchars($tenant->getId()) ?>
        </dd>

        <dt class="col-lg-4">
          Code
        </dt>
        <dd class="col-lg-8 font-monospace">
          <?= htmlspecialchars($tenant->getCode()) ?>
        </dd>

        <dt class="col-lg-4">
          Website
        </dt>
        <dd class="col-lg-8 font-monospace">
          <a href="<?= htmlspecialchars($tenant->getWebsite()) ?>" target="_blank"><?= htmlspecialchars($tenant->getWebsite()) ?></a>
        </dd>

        <dt class="col-lg-4">
          Email
        </dt>
        <dd class="col-lg-8 font-monospace">
          <a href="mailto:<?= htmlspecialchars($tenant->getEmail()) ?>"><?= htmlspecialchars($tenant->getEmail()) ?></a>
        </dd>

        <dt class="col-lg-4">
          Stripe Account
        </dt>
        <dd class="col-lg-8">
          <div class="font-monospace mb-2">
            <?= htmlspecialchars($tenant->getStripeAccount()) ?>
          </div>
          <div class="">
            <a href="<?= htmlspecialchars(autoUrl("admin/tenants/$id/stripe")) ?>" class="btn btn-primary">Stripe settings</a>
          </div>
        </dd>
      </dl>

      <?php pre(get_class_methods($tenant)); ?>
      <?php pre($tenant); ?>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\RootFooter();
$footer->render();
