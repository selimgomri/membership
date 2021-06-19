<?php

$db = app()->db;
$tenant = app()->tenant;

$getYear = $db->prepare("SELECT `Name`, `StartDate`, `EndDate` FROM `membershipYear` WHERE `ID` = ? AND `Tenant` = ?");
$getYear->execute([
  $id,
  $tenant->getId(),
]);
$year = $getYear->fetch(PDO::FETCH_ASSOC);

if (!$year) halt(404);

$pagetitle = htmlspecialchars($year['Name']) . " - Membership Years - Membership Centre";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships")) ?>">Memberships</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships/years")) ?>">Years</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships/years/$id")) ?>"><?= htmlspecialchars($year['Name']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Editing <?= htmlspecialchars($year['Name']) ?>
        </h1>
        <p class="lead mb-0">
          EDIT X
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8">
      <p>
        The Membership Centre lets clubs track which memberships their members hold in a given year.
      </p>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
