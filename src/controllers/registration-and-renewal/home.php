<?php

use SCDS\Footer;

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

$getCurrentRegistrationAndRenewals = $db->prepare("SELECT renewalData.ID, renewalPeriods.ID PID, renewalPeriods.Name, renewalPeriods.Year FROM renewalData LEFT JOIN renewalPeriods ON renewalPeriods.ID = renewalData.Renewal WHERE renewalData.User = ? AND NOT JSON_VALUE(renewalData.Document, '$.complete')");
$getCurrentRegistrationAndRenewals->execute([
  $user->getId(),
]);

// $ren = Renewal::createUserRenewal($user->getId());
// $ren->save();

$pagetitle = "Registration and Renewal";
include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Renewal</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Registration and renewal
        </h1>
        <p class="lead mb-0">
          Register or renew your membership
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row">
    <div class="col-lg-8">
      <h2>Your current registration and renewals</h2>
      <?php if ($renewal = $getCurrentRegistrationAndRenewals->fetch(PDO::FETCH_ASSOC)) { ?>
      <p class="lead">
        You must complete all registration or renewal forms listed below.
      </p>
        <div class="list-group">
          <?php do { ?>
            <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $renewal['ID'])) ?>" class="list-group-item list-group-item-action">
              <?php if ($renewal['PID']) { ?>
                <?= htmlspecialchars($renewal['Name']) ?>
              <?php } else { ?>
                Registration
              <?php } ?>
            </a>
          <?php } while ($renewal = $getCurrentRegistrationAndRenewals->fetch(PDO::FETCH_ASSOC)); ?>
        </div>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>There are no registration or renewal forms for you to complete</strong>
          </p>
          <p class="mb-0">
            If you expected to see a form to complete, please contact your club.
          </p>
        </div>
      <?php } ?>

      <?php if ($user->hasPermission('Admin')) { ?>
        <h2>Membership renewal periods</h2>
        <p class="lead">
          View previous membership renewals or add a new renewal period.
        </p>
      <?php } ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->render();
