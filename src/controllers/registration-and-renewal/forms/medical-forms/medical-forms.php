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

$medical = null;
try {
  $medical = $ren->getSection('medical_forms');
} catch (Exception $e) {
  halt(404);
}

$pagetitle = htmlspecialchars("Medical Forms - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Medical Forms</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Medical Forms
        </h1>
        <p class="lead mb-0">
          Check your details are still up to date
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <p class="lead">
        Please update any incorrect details.
      </p>

      <?php if (sizeof($medical['members']) > 0) { ?>
        <div class="list-group mb-3">
          <?php for ($i = 0; $i < sizeof($medical['members']); $i++) {
            $member = $ren->getMember($medical['members'][$i]['id']);
          ?>
            <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/medical-forms/' . $medical['members'][$i]['id'])) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <?php if (bool($member['current'])) { ?>
                <?= htmlspecialchars($member['name']) ?>
                <span><?php if (false) { ?><span class="text-success">Complete <i class="fa fa-fw fa-check-circle" aria-hidden="true"></i></span><?php } else { ?><span class="text-warning">Needs Checking <i class="fa fa-fw fa-minus-circle" aria-hidden="true"></i></span><?php } ?></span>
              <?php } else { ?>
                <?= htmlspecialchars($member['name']) ?> - This member is no longer associated with your account
              <?php } ?>
            </a>
          <?php } ?>
        </div>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>You have no members to complete medical forms for</strong>
          </p>
          <p class="mb-0">
            You can proceed.
          </p>
        </div>
      <?php } ?>

      <form method="post" class="needs-validation" novalidate>

        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success">Save and complete section</button>
        </p>
      </form>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'medical-forms') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
