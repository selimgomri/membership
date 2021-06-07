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

$userDetails = $db->prepare("SELECT * FROM users WHERE UserID = ?");
$userDetails->execute([
  $ren->getUser(),
]);
$row = $userDetails->fetch(PDO::FETCH_ASSOC);

$email = $row['EmailAddress'];
$forename = $row['Forename'];
$surname = $row['Surname'];
$userID = $row['UserID'];
$mobile = $row['Mobile'];
if ($row['EmailComms']) {
  $emailChecked = " checked ";
}
if ($row['MobileComms']) {
  $mobileChecked = " checked ";
}

$pagetitle = htmlspecialchars("Account Review - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Account Review</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Account Review
        </h1>
        <p class="lead mb-0">
          Check your details are still up to date
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row justify-content-between">
    <div class="col-lg-8">

    <p class="lead">
      Please update any incorrect details.
    </p>

      <form method="post" class="needs-validation" novalidate>
        <div class="">
          <div class="mb-3">
            <label class="form-label" for="forename">Name</label>
            <input type="text" class="form-control" name="forename" id="forename" placeholder="Forename" value="<?= htmlspecialchars($forename) ?>" required>
            <div class="invalid-feedback">
              Please provide your first name.
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="surname">Surname</label>
            <input type="text" class="form-control" name="surname" id="surname" placeholder="Surname" value="<?= htmlspecialchars($surname) ?>" required>
            <div class="invalid-feedback">
              Please provide your last name.
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" value="<?= htmlspecialchars($email) ?>" disabled>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="emailContactOK" aria-describedby="emailContactOKHelp" name="emailContactOK" <?= $emailChecked ?>>
              <label class="form-check-label" for="emailContactOK">I would like to receive news and messages from squad coaches by email</label>
              <small id="emailContactOKHelp" class="form-text text-muted">You'll still receive emails relating to your account if you don't receive news</small>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="mobile">Mobile Number</label>
            <input type="tel" class="form-control" name="mobile" id="mobile" aria-describedby="mobileHelp" placeholder="Mobile Number" value="<?= htmlspecialchars($mobile) ?>" required>
            <div class="invalid-feedback">
              Please provide your phone nuumber. We may need this in emergencies.
            </div>
            <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="smsContactOK" aria-describedby="smsContactOKHelp" name="smsContactOK" <?= $mobileChecked ?>>
              <label class="form-check-label" for="smsContactOK">I am happy to be contacted by text message</label>
              <small id="smsContactOKHelp" class="form-text text-muted">We'll still use this to contact you in an emergency</small>
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
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'account-review') ?>
    </div>
  </div>
</div>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
