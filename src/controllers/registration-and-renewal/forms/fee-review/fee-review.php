<?php

use SCDS\Footer;

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

$getRenewal = $db->prepare("SELECT renewalData.ID, renewalPeriods.ID PID, renewalPeriods.Name, renewalPeriods.Year, renewalData.User, renewalData.Document FROM renewalData LEFT JOIN renewalPeriods ON renewalPeriods.ID = renewalData.Renewal LEFT JOIN users ON users.UserID = renewalData.User WHERE renewalData.ID = ? AND users.Tenant = ?");
$getRenewal->execute([
  $id,
  $tenant->getId(),
]);
$renewal = $getRenewal->fetch(PDO::FETCH_ASSOC);

if (!$renewal) {
  halt(404);
}

if (!$user->hasPermission('Admin') && $renewal['User'] != $user->getId()) {
  halt(404);
}

$ren = Renewal::getUserRenewal($id);

$pagetitle = htmlspecialchars("Fee Review - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Fee Review</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Fee Review
        </h1>
        <p class="lead mb-0">
          Double check your monthly fees
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <form method="post" class="needs-validation" novalidate>

        <p class="lead">Here are the monthly fees you pay.</p>

        <div class="mb-3">
          <?= myMonthlyFeeTable(null, $ren->getUser()) ?>
        </div>


        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success">Confirm and complete section</button>
        </p>
      </form>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'fee-review') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
