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

$photos = null;
try {
  $photos = $ren->getSection('photography_permissions');
} catch (Exception $e) {
  halt(404);
}

$pagetitle = htmlspecialchars("Photography Permissions - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Photography Permissions</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Photography Permissions
        </h1>
        <p class="lead mb-0">
          Grant permissions for each of your members
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <p class="lead">
        In line with Swim England guidance, we offer very granular permissions controls.
      </p>

      <p>
        Please read the Swim England/<?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> Photography Policy before you continue to give or withold consent for photography.
      </p>

      <p>
        <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> may wish to take photographs of individuals and groups of swimmers under the age of 18, which may include your child during their membership of <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>. Photographs will only be taken and published in accordance with Swim England policy which requires the club to obtain the consent of the Parent or Guardian to take and use photographs under the following circumstances.
      </p>

      <p>
        It is entirely up to you whether or not you choose to allow us to take photographs and/or video of your child. You can change your choices at any time by heading to the Members section of the membership system.
      </p>

      <?php if (sizeof($photos['members']) > 0) { ?>
        <div class="list-group mb-3">
          <?php for ($i = 0; $i < sizeof($photos['members']); $i++) {
            $member = $ren->getMember($photos['members'][$i]['id']);
          ?>
            <a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/medical-forms/' . $photos['members'][$i]['id'])) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
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
            <strong>You have no members to complete photography permissions forms for</strong>
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
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'photography-permissions') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
