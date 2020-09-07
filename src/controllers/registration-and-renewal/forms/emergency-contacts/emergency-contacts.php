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

$userInfo = $db->prepare("SELECT Forename, Surname, Mobile FROM `users` WHERE `UserID` = ?");
$userInfo->execute([
  $ren->getUser(),
]);
$user = $userInfo->fetch(PDO::FETCH_ASSOC);

$mobile = PhoneNumber::parse($user['Mobile'], 'GB');

$contacts = new EmergencyContacts($db);
$contacts->byParent($ren->getUser());

$contactsArray = $contacts->getContacts();

$moreRequired = 2 - sizeof($contactsArray);

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

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AddNewSuccess'])) {
        echo $_SESSION['TENANT-' . app()->tenant->getId()]['AddNewSuccess'];
        unset($_SESSION['TENANT-' . app()->tenant->getId()]['AddNewSuccess']);
      } ?>

      <p class="lead">
        We need at least two contact details, in addition to yourself.
      </p>

      <p>
        We'll use these emergency contacts for all members connected to your account if, we can't reach you on your phone number. You can change your phone number at any time in <em>My Account</em>.
      </p>

      <form method="post" class="needs-validation" novalidate>

        <ul class="list-group mb-3" id="contacts-list">
          <li class="list-group-item">
            <p class="mb-0">
              <strong>
                <?= htmlspecialchars($user['Forename'] . " " . $user['Surname']) ?>
              </strong>
              <em>(From My Account)</em>
            </p>
            <p class="mb-0">
              <a href="tel:<?= $mobile->format(PhoneNumberFormat::RFC3966) ?>">
                <?= $mobile->format(PhoneNumberFormat::NATIONAL) ?>
              </a>
            </p>
          </li>
          <?php for ($i = 0; $i < sizeof($contactsArray); $i++) {
          ?>
            <li class="list-group-item">
              <div class="row align-items-center">
                <div class="col-9">
                  <p class="mb-0">
                    <strong>
                      <?= htmlspecialchars($contactsArray[$i]->getName()) ?>
                    </strong>
                    <em>
                      <?= htmlspecialchars($contactsArray[$i]->getRelation()) ?>
                    </em>
                  </p>
                  <p class="mb-0">
                    <a href="tel:<?= htmlspecialchars($contactsArray[$i]->getRFCContactNumber()) ?>">
                      <?= htmlspecialchars($contactsArray[$i]->getNationalContactNumber()) ?>
                    </a>
                  </p>
                </div>
                <div class="col text-sm-right">
                  <div class="btn-group">
                    <button class="btn btn-dark" type="button" data-contact-id="<?= htmlspecialchars($contactsArray[$i]->getId()) ?>" data-type="edit-button" data-contact-name="<?= htmlspecialchars($contactsArray[$i]->getName()) ?>" data-contact-relation="<?= htmlspecialchars($contactsArray[$i]->getRelation()) ?>" data-contact-number="<?= htmlspecialchars($contactsArray[$i]->getContactNumber()) ?>">
                      Edit
                    </button>
                    <button class="btn btn-danger" type="button" data-contact-id="<?= htmlspecialchars($contactsArray[$i]->getId()) ?>" data-type="delete-button" data-contact-name="<?= htmlspecialchars($contactsArray[$i]->getName()) ?>">
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </li>
          <?php
          } ?>
        </ul>

        <p class="">
          <button id="new-button" class="btn btn-primary" type="button" data-type="new-button">Add a New Contact</button>
        </p>

        <p>Please inform people if you have added them as an emergency contact.</p>

        <?php if (sizeof($contactsArray) < 2) { ?>
          <p>
            <strong>In line with Swim England guidance, we need you to add <?= htmlspecialchars($numFormat->format($moreRequired)) ?> more contact<?php if ($moreRequired != 1) { ?>s<?php } ?>.</strong>
          </p>
        <?php } ?>

        <?= \SCDS\CSRF::write() ?>

        <p>
          <button type="submit" class="btn btn-success" <?php if (sizeof($contactsArray) < 2) { ?>disabled<?php } ?>>Confirm and complete section</button>
        </p>
      </form>

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

<script>
  let modalHeaderContainer = document.getElementById('edit-delete-modal-header');
  let modalHeader = document.getElementById('edit-delete-modal-title');
  let modalBody = document.getElementById('edit-delete-modal-body');
  let modalFooter = document.getElementById('edit-delete-modal-footer');
  let modal = document.getElementById('edit-delete-modal');

  function setForm(body) {

    let formGroup, label, input, form, invalid;

    form = document.createElement('FORM');
    form.classList.add('needs-validation');
    form.setAttribute('novalidate', '');
    form.id = 'contact-form';


    // Name
    formGroup = document.createElement('DIV');
    formGroup.classList.add('form-group');

    label = document.createElement('LABEL');
    label.textContent = 'Name';

    input = document.createElement('INPUT');
    input.classList.add('form-control');
    input.type = 'text';
    input.required = true;
    input.placeholder = 'Name';
    input.id = input.name = 'contact-name';

    invalid = document.createElement('DIV');
    invalid.classList.add('invalid-feedback')
    invalid.textContent = 'You must provide the name of the emergency contact';

    formGroup.appendChild(label);
    formGroup.appendChild(input);
    formGroup.appendChild(invalid);
    form.appendChild(formGroup);

    // Relation
    formGroup = document.createElement('DIV');
    formGroup.classList.add('form-group');

    label = document.createElement('LABEL');
    label.textContent = 'Relation';

    input = document.createElement('INPUT');
    input.classList.add('form-control');
    input.type = 'text';
    input.required = true;
    input.placeholder = 'Relation';
    input.id = input.name = 'contact-relation';

    invalid = document.createElement('DIV');
    invalid.classList.add('invalid-feedback')
    invalid.textContent = 'You must provide the relation so we can decide who is best to call';

    formGroup.appendChild(label);
    formGroup.appendChild(input);
    formGroup.appendChild(invalid);
    form.appendChild(formGroup);

    // Number
    formGroup = document.createElement('DIV');
    formGroup.classList.add('form-group');

    label = document.createElement('LABEL');
    label.textContent = 'Contact Number';

    input = document.createElement('INPUT');
    input.classList.add('form-control');
    input.type = 'tel';
    input.required = true;
    input.placeholder = 'Phone';
    input.id = input.name = 'contact-number';
    input.pattern = '\\+{0,1}[0-9]*';

    invalid = document.createElement('DIV');
    invalid.classList.add('invalid-feedback')
    invalid.textContent = 'You must provide a valid UK phone number';

    formGroup.appendChild(label);
    formGroup.appendChild(input);
    formGroup.appendChild(invalid);
    form.appendChild(formGroup);

    body.appendChild(form);

    form.addEventListener('submit', event => {
      if (form.checkValidity() === false) {
        event.preventDefault();

        // Only add .was-validated here - otherwise gives flash of green
        form.classList.add('was-validated');
      }
    });

  }

  document.getElementById('contacts-list').addEventListener('click', event => {
    let contactId;

    if (event.target.dataset.type && event.target.dataset.type == 'edit-button') {
      // Handle edit
      contactId = event.target.dataset.contactId;

      // Unset danger header
      modalHeaderContainer.classList.remove('bg-danger', 'text-white');
      modalHeader.textContent = 'Edit an emergency contact';

      modalBody.innerHTML = '';

      setForm(modalBody);

      document.getElementById('contact-name').value = event.target.dataset.contactName;
      document.getElementById('contact-relation').value = event.target.dataset.contactRelation;
      document.getElementById('contact-number').value = event.target.dataset.contactNumber;

      // Set footer
      // <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
      let dismissButton = document.createElement('BUTTON');
      dismissButton.classList.add('btn', 'btn-dark');
      dismissButton.dataset.dismiss = 'modal';
      dismissButton.type = 'button';
      dismissButton.textContent = 'Cancel';

      let saveButton = document.createElement('BUTTON');
      saveButton.classList.add('btn', 'btn-success');
      saveButton.type = 'submit';
      saveButton.setAttribute('form', 'contact-form');
      saveButton.textContent = 'Save';

      modalFooter.innerHTML = '';
      modalFooter.appendChild(dismissButton);
      modalFooter.appendChild(saveButton);

      modal.addEventListener('submit', event => {
        event.preventDefault();
        event.stopPropagation();
        console.log(event);
      });

      $('#edit-delete-modal').modal('show')
    }
    if (event.target.dataset.type && event.target.dataset.type == 'delete-button') {
      // Handle delete
      contactId = event.target.dataset.contactId;

      // Set danger header
      modalHeaderContainer.classList.add('bg-danger', 'text-white');
      modalHeader.textContent = 'Are you sure?';

      modalBody.innerHTML = '';

      let p = document.createElement('P');
      p.classList.add('text-danger', 'mb-0');
      p.textContent = 'Are you sure you want to delete ' + event.target.dataset.contactName + ' from your emergency contacts?';
      modalBody.appendChild(p);

      // Set footer
      // <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
      let dismissButton = document.createElement('BUTTON');
      dismissButton.classList.add('btn', 'btn-dark');
      dismissButton.dataset.dismiss = 'modal';
      dismissButton.type = 'button';
      dismissButton.textContent = 'Cancel';

      let deleteButton = document.createElement('BUTTON');
      deleteButton.classList.add('btn', 'btn-danger');
      deleteButton.type = 'submit';
      deleteButton.textContent = 'Delete';

      modalFooter.innerHTML = '';
      modalFooter.appendChild(dismissButton);
      modalFooter.appendChild(deleteButton);

      modal.addEventListener('submit', event => {
        // Do nothing
      });

      deleteButton.addEventListener('click', event => {
        console.log(event);
      })

      $('#edit-delete-modal').modal('show')
    }
  });

  document.getElementById('new-button').addEventListener('click', event => {
    // Unset danger header
    modalHeaderContainer.classList.remove('bg-danger', 'text-white');
    modalHeader.textContent = 'Add a new emergency contact';

    modalBody.innerHTML = '';

    setForm(modalBody);

    // Set footer
    // <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
    let dismissButton = document.createElement('BUTTON');
    dismissButton.classList.add('btn', 'btn-dark');
    dismissButton.dataset.dismiss = 'modal';
    dismissButton.type = 'button';
    dismissButton.textContent = 'Cancel';

    let saveButton = document.createElement('BUTTON');
    saveButton.classList.add('btn', 'btn-success');
    saveButton.type = 'submit';
    saveButton.setAttribute('form', 'contact-form');
    saveButton.textContent = 'Add';

    modalFooter.innerHTML = '';
    modalFooter.appendChild(dismissButton);
    modalFooter.appendChild(saveButton);

    modal.addEventListener('submit', event => {
      event.preventDefault();
      event.stopPropagation();
      console.log(event);
    });

    $('#edit-delete-modal').modal('show')
  });
</script>

<?php

$footer = new Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
