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

$pagetitle = htmlspecialchars($ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Renewal</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($ren->getRenewalName()) ?>
        </h1>
        <p class="lead mb-0">
          Register or renew your membership
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col">
      <div class="list-group">
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/account-review')) ?>" class="list-group-item list-group-item-action">
          Account Review
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/member-review')) ?>" class="list-group-item list-group-item-action">
          Member Review
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/fee-review')) ?>" class="list-group-item list-group-item-action">
          Fee Review
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/address-review')) ?>" class="list-group-item list-group-item-action">
          Address Review
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/emergency-contacts')) ?>" class="list-group-item list-group-item-action">
          Emergency Contacts
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/medical-forms')) ?>" class="list-group-item list-group-item-action">
          Medical Forms
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/conduct-forms')) ?>" class="list-group-item list-group-item-action">
          Conduct Forms
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/data-protection-and-privacy')) ?>" class="list-group-item list-group-item-action">
          Data Protection and Privacy
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/terms-and-conditions')) ?>" class="list-group-item list-group-item-action">
          Terms and Conditions
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/photography-permissions')) ?>" class="list-group-item list-group-item-action">
          Photography Permissions
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/administration-form')) ?>" class="list-group-item list-group-item-action">
          Administration Form
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/direct-debit')) ?>" class="list-group-item list-group-item-action">
          Direct Debit
        </a>
        <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/renewal-fees')) ?>" class="list-group-item list-group-item-action">
          <?= htmlspecialchars($ren->getTypeName()) ?> Fees
        </a>
      </div>
    </div>
    <div class="col-lg-8">

      <div class="card card-body tidy-debug-pre">
        <?= pre($ren) ?>
      </div>

    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->render();
