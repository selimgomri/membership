<?php

use SCDS\Footer;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;
$numFormat = new NumberFormatter("en", NumberFormatter::SPELLOUT);

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

$pagetitle = htmlspecialchars("Emergency Contacts - " . $ren->getRenewalName());

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal')) ?>">Registration</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id)) ?>"><?= htmlspecialchars($ren->getRenewalName()) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Emergency Contacts</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Emergency Contacts
        </h1>
        <p class="lead mb-0">
          Check your Emergency Contact details are still up to date
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row justify-content-between">
    <div class="col-lg-8">

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
        echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
        unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
      } ?>

      <div id="view-window">

        <?php

        // GET VIEW ON INITIAL LOAD
        include 'view.php';
        getView($id);

        ?>

      </div>

    </div>
    <div class="col col-xl-3">
      <?= CLSASC\BootstrapComponents\RenewalProgressListGroup::renderLinks($ren, 'emergency-contacts') ?>
    </div>
  </div>
</div>

<div class="modal" id="edit-delete-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="edit-delete-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div id="edit-delete-modal-header" class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="edit-delete-modal-title"></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="edit-delete-modal-body">
      </div>
      <div class="modal-footer" id="edit-delete-modal-footer">
        <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div id="js-opts" data-view-url="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/emergency-contacts/view')) ?>" data-add-url="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/emergency-contacts/add')) ?>" data-edit-url="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/emergency-contacts/edit')) ?>" data-delete-url="<?= htmlspecialchars(autoUrl('registration-and-renewal/' . $id . '/emergency-contacts/delete')) ?>"></div>

<?php

$footer = new Footer();
$footer->addJs('public/js/registration-and-renewal/emergency-contacts.js');
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
