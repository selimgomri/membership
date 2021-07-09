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

$members = $ren->getMembers();

$pagetitle = htmlspecialchars("Member Review - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Member Review</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Member Review
        </h1>
        <p class="lead mb-0">
          Check all your members are included in this <?= htmlspecialchars($ren->getTypeName(false)) ?>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']) && $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']) { ?>
        <div class="alert alert-danger">
          <p class="mb-0">
            <strong>An error occurred when we tries to save the changes</strong>
          </p>
        </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
      } ?>

      <form method="post" class="needs-validation" novalidate>

        <?php if (sizeof($members) > 0) { ?>

          <p class="lead">
            Please check that all members for which you expect to complete <?= htmlspecialchars($ren->getTypeName(false)) ?> are listed below.
          </p>

          <ul class="list-group mb-3">
            <?php foreach ($members as $member) { ?>
              <li class="list-group-item <?php if (!bool($member['current'])) { ?>disabled<?php } ?>" id="member-<?= htmlspecialchars($member['id']) ?>">
                <?php if (bool($member['current'])) { ?>
                  <?= htmlspecialchars($member['name']) ?>
                <?php } else { ?>
                  <?= htmlspecialchars($member['name']) ?> - This member is no longer associated with your account
                <?php } ?>
              </li>
            <?php } ?>
          </ul>

          <p>
            If any members are not listed, please contact your club membership secretary before you continue with <?= htmlspecialchars($ren->getTypeName(false)) ?>.
          </p>

        <?php } else { ?>

          <div class="alert alert-danger">
            <p class="mb-0">
              <strong>There are no members associated with this renewal</strong>
            </p>
            <p class="mb-0">
              This means there has been a problem. Please contact your club membership secretary for support before you try to continue with <?= htmlspecialchars($ren->getTypeName(false)) ?>.
            </p>
          </div>

        <?php } ?>

        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success" <?php if (sizeof($members) == 0) { ?>disabled title="You cannot continue <?= htmlspecialchars($ren->getTypeName(false)) ?> without members" <?php } ?>>Confirm and complete section</button>
        </p>
      </form>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'member-review') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
