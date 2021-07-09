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

$renewalUser = new User($ren->getUser());

// Get mandates
$getMandates = $db->prepare("SELECT ID, Mandate, Last4, SortCode, `Address`, Reference, `URL`, `Status` FROM stripeMandates WHERE Customer = ? AND (`Status` = 'accepted' OR `Status` = 'pending') ORDER BY CreationTime DESC LIMIT 1");
$getMandates->execute([
  $renewalUser->getStripeCustomer()->id,
]);
$mandate = $getMandates->fetch(PDO::FETCH_ASSOC);

$used = true;

// Work out if has mandates
$getCountNewMandates = $db->prepare("SELECT COUNT(*) FROM stripeMandates INNER JOIN stripeCustomers ON stripeMandates.Customer = stripeCustomers.CustomerID WHERE stripeCustomers.User = ? AND stripeMandates.MandateStatus != 'inactive';");
$getCountNewMandates->execute([
  $ren->getUser(),
]);
$hasStripeMandate = $getCountNewMandates->fetchColumn() > 0;

$pagetitle = htmlspecialchars($ren->getTypeName() . " Fees - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($ren->getTypeName()) ?> Fees</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($ren->getTypeName()) ?> Fees
        </h1>
        <p class="lead mb-0">
          Pay your <?= htmlspecialchars($ren->getTypeName(false)) ?> fees
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <p class="lead">
        There's just one more step to go. We now need you to pay your membership <?= htmlspecialchars($ren->getTypeName(false)) ?> fees.
      </p>

      <form method="post" class="needs-validation" novalidate>

        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success">Save and complete section</button>
        </p>
      </form>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'registration-fees') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
