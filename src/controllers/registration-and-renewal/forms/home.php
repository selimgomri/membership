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
          Register or renew your membership for the year
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <p class="lead">
        Welcome to <?= htmlspecialchars($ren->getRenewalName()) ?>.
      </p>

      <?php if (false) { ?>

        <p>
          Membership renewal ensures all our information about you is up to date, that you / you and your members understand your rights and responsibilities at the club, and that you can pay your <abbr title="Including Swim England Membership Fees"> membership fee</abbr> for the year ahead.
        </p>

      <?php } else { ?>

        <p>
          Online membership registration allows us to collect all required information about you / you and your members in a GDPR compliant manner. This involves us asking for some emergency contact details and medical information.
        </p>

        <p>
          It also ensures that you / you and your members understand your rights and responsibilities at the club, and that you can pay your <abbr title="Including Swim England Membership Fees"> membership fee</abbr> for the year ahead.
        </p>

      <?php } ?>

      <p>
        Do not worry if you make a mistake while filling out any forms. You can edit all information at any time.
      </p>
      <p>
        We'll save your progress as you fill out the required forms.
      </p>

      <div class="card card-body mb-3">
        <p class="mb-0">
          <strong>Your unique renewal and registration number is</strong>
        </p>

        <p>
          <input type="text" readonly class="form-control-plaintext font-monospace p-0" id="reg-renewal-id" value="<?= htmlspecialchars($id) ?>">
        </p>

        <p class="mb-0">
          If you have any issues, your membership secretary will be able to bring up your <?= htmlspecialchars($ren->getTypeName(false)) ?> forms on their own device.
        </p>
      </div>

      <!-- <div class="card card-body tidy-debug-pre mb-3">
        <?= pre($ren) ?>
      </div> -->
    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren) ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->render();
