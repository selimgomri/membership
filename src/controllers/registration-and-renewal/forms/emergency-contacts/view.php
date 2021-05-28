<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

function getView($id)
{

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

?>

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
            <div class="col text-sm-end">
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

<?php

}

?>