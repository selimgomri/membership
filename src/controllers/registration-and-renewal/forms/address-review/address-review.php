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

$addr = [];
$getAddress = $db->prepare("SELECT `Value` FROM `userOptions` WHERE `User` = ? AND `Option` = ?");
$getAddress->execute([
  $ren->getUser(),
  'MAIN_ADDRESS',
]);
$json = $getAddress->fetchColumn();
if ($json != null) {
  $addr = json_decode($json);
}

$pagetitle = htmlspecialchars("Address Review - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Address Review</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Address Review
        </h1>
        <p class="lead mb-0">
          Manage your address
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row justify-content-between">
    <div class="col-lg-8">

    <p class="lead">
      Please confirm, add or update your main home address.
    </p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']) && $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']) { ?>
        <div class="alert alert-danger">
          <p class="mb-0">
            <strong>An error occurred when we tries to save the changes</strong>
          </p>
        </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
      } ?>

      <form method="post" class="needs-validation" novalidate>

        <?php if (isset($addr->streetAndNumber)) { ?>
          <h2>Your address is</h2>
          <address>
            <?= htmlspecialchars($addr->streetAndNumber) ?><br>
            <?php if (isset($addr->flatOrBuilding)) { ?>
              <?= htmlspecialchars($addr->flatOrBuilding) ?><br>
            <?php } ?>
            <?= htmlspecialchars(mb_strtoupper($addr->city)) ?><br>
            <?= htmlspecialchars(mb_strtoupper($addr->postCode)) ?><br>
          </address>

          <p>
            <button type="submit" class="btn btn-success">Unchanged? Save and complete section</button>
          </p>

          <h2>Edit address</h2>
        <?php } else { ?>
          <h2>Add address</h2>
        <?php } ?>

        <p>You must use a UK address or a British Forces address.</p>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="street-and-number">Address line 1 (street and number)</label>
              <input class="form-control" name="street-and-number" id="street-and-number" type="text" autocomplete="address-line1" <?php if (isset($addr->streetAndNumber)) { ?>value="<?= htmlspecialchars($addr->streetAndNumber) ?>" <?php } ?> required>
              <div class="invalid-feedback">
                Please provide your street and house number/name.
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="flat-building">Address line 2 (optional)</label>
              <input class="form-control" name="flat-building" id="flat-building" type="text" autocomplete="address-line2" <?php if (isset($addr->flatOrBuilding)) { ?>value="<?= htmlspecialchars($addr->flatOrBuilding) ?>" <?php } ?>>
            </div>

            <div class="mb-3">
              <label class="form-label" for="town-city">Town/City</label>
              <input class="form-control" name="town-city" id="town-city" type="text" autocomplete="address-level2" <?php if (isset($addr->city)) { ?>value="<?= htmlspecialchars($addr->city) ?>" <?php } ?> required>
              <div class="invalid-feedback">
                Please provide your postal town/city.
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="county-province">County</label>
              <input class="form-control" name="county-province" id="county-province" type="text" autocomplete="address-level1" <?php if (isset($addr->county)) { ?>value="<?= htmlspecialchars($addr->county) ?>" <?php } ?> required>
              <div class="invalid-feedback">
                Please provide your county.
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="post-code">Post Code</label>
              <input class="form-control" name="post-code" id="post-code" type="text" autocomplete="postal-code" <?php if (isset($addr->postCode)) { ?>value="<?= htmlspecialchars($addr->postCode) ?>" <?php } ?> required>
              <div class="invalid-feedback">
                Please provide your UK or British Forces post code.
              </div>
            </div>
          </div>
        </div>

        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success">Save and complete section</button>
        </p>
      </form>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'address-review') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
