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

$pagetitle = htmlspecialchars("Direct Debit - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Direct Debit</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Direct Debit
        </h1>
        <p class="lead mb-0">
          Manage your direct debit
        </p>
      </div>
      <div class="d-none d-sm-flex col-sm-auto ms-auto">
        <img style="max-height:50px;" src="<?= htmlspecialchars(autoUrl("img/directdebit/directdebit.png", false)) ?>" srcset="<?= htmlspecialchars(autoUrl("img/directdebit/directdebit@2x.png", false)) ?> 2x, <?= htmlspecialchars(autoUrl("img/directdebit/directdebit@3x.png", false)) ?> 3x" alt="Direct
				Debit Logo">
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <form method="post" class="needs-validation" novalidate>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) && $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) { ?>
          <div class="alert alert-success">
            <p class="mb-0">
              <strong>We've set up your new direct debit</strong>
            </p>
            <p>
              It will take a few days for the mandate to be confirmed at your bank.
            </p>

            <p class="mb-0 small">
              At busy times, your mandate may take a few minutes to appear in our systems.
            </p>
          </div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']);
        } ?>

        <?php if ($mandate) {
          do { ?>
            <div class="card card-body mb-3">
              <h2><?php if ($used) { ?>Your Direct Debit Mandate<?php $user = false;
                                                              } else { ?>Old Mandate<?php } ?> <span class="badge bg-secondary"><?php if ($mandate['Status'] == 'pending') { ?>Pending<?php } else if ($mandate['Status'] == 'accepted') { ?>Active<?php } ?></span></h2>
              <dl class="row">
                <dt class="col-sm-3">Sort code</dt>
                <dd class="col-sm-9 font-monospace"><?= htmlspecialchars(implode("-", str_split($mandate['SortCode'], 2))) ?></dd>

                <dt class="col-sm-3">Account number</dt>
                <dd class="col-sm-9 font-monospace">&middot;&middot;&middot;&middot;<?= htmlspecialchars($mandate['Last4']) ?></dd>

                <dt class="col-sm-3">Payment reference</dt>
                <dd class="col-sm-9 font-monospace"><?= htmlspecialchars($mandate['Reference']) ?></dd>
              </dl>

              <p class="mb-0">
                <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/direct-debit/view-ddi?payment_method=' . $mandate['ID'])) ?>" target="_blank">
                  View Direct Debit Instruction
                </a>
              </p>
            </div>
        <?php } while ($mandate = $getMandates->fetch(PDO::FETCH_ASSOC));
        } ?>

        <p>
          <a class="btn btn-primary" href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/direct-debit/set-up')) ?>">Set up a new mandate</a>
        </p>

        <p>
          We will always use the most recent mandate that you set up for your payments. Mandates are fully set up once they become active.
        </p>

        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success" <?php if (!$hasStripeMandate) { ?>disabled<?php } ?>>Save and complete section</button>
        </p>

        <?php if (true || (!$hasStripeMandate && app()->tenant->getBooleanKey('ALLOW_DIRECT_DEBIT_OPT_OUT'))) { ?>
          <p><button type="submit" name="avoid-dd" value="1" class="btn btn-outline-dark btn-sm">Skip Direct Debit setup and complete section</button></p>
        <?php } ?>
      </form>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'direct-debit') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
