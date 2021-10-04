<?php

use SCDS\CSRF;

$user = app()->user;
$db = app()->db;
$tenant = app()->tenant;

$session = \SCDS\Onboarding\Session::retrieve($id);

$states = \SCDS\Onboarding\Session::getStates();

$getMembershipYears = $db->prepare("SELECT `ID` `id`, `Name` `name` FROM membershipYear WHERE Tenant = ? AND EndDate >= ?");
$getMembershipYears->execute([
  $tenant->getId(),
  (new DateTime('now', new DateTimeZone('Europe/London')))->format('Y-m-d')
]);
$membershipYear = $getMembershipYears->fetch(PDO::FETCH_OBJ);
$hasYear = $membershipYear != null;

$pagetitle = "Onboarding Session";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('onboarding')) ?>">Onboarding</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($session->getUser()->getName()) ?></li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Edit onboarding session
        </h1>
        <p class="lead mb-0">
          Onboarding is the replacement for assisted registration.
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row">
    <div class="col-lg-8">

      <form method="post" class="needs-validation" id="form" novalidate>

        <?= CSRF::write(); ?>

        <div class="mb-3">
          <label for="user-name" class="form-label">Member/Parent Name</label>
          <input type="text" class="form-control" id="user-name" name="user-name" value="<?= htmlspecialchars($session->getUser()->getName()) ?>" readonly>
        </div>

        <div class="mb-3">
          <label for="session-id" class="form-label">Onboarding Session ID</label>
          <input type="text" class="form-control" id="session-id" name="session-id" value="<?= htmlspecialchars($session->id) ?>" readonly>
        </div>

        <div class="mb-3">
          <label for="creator-name" class="form-label">Onboarding Creator Name</label>
          <input type="text" class="form-control" id="creator-name" name="creator-name" value="<?= htmlspecialchars($session->getCreator()->getName()) ?>" readonly>
        </div>

        <div class="mb-3">
          <label for="start-date" class="form-label">Start Date</label>
          <input type="date" class="form-control" id="start-date" name="start-date" value="<?= htmlspecialchars($session->start->format('Y-m-d')) ?>" required aria-describedby="start-help" <?php if ($session->renewal) { ?>disabled<?php } ?>>
          <div id="start-help" class="form-text">This is the date the member started at the club.<?php if (app()->tenant->getBooleanKey('USE_DIRECT_DEBIT')) { ?> If you're charging for feees before the first Direct Debit, this should be the date payment is calculated from.<?php } ?></div>
        </div>

        <div class="mb-3">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="charge-fees" id="charge-fees-yes" <?php if ($session->chargeOutstanding) { ?>checked<?php } ?> value="1" data-toggle="pro-rata-box" <?php if (!app()->tenant->getBooleanKey('USE_DIRECT_DEBIT') || $session->renewal || true) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="charge-fees-yes">
              Charge fees up to first Direct Debit date
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="charge-fees" id="charge-fees-no" <?php if (!$session->chargeOutstanding) { ?>checked<?php } ?> value="0" data-toggle="pro-rata-box" <?php if (!app()->tenant->getBooleanKey('USE_DIRECT_DEBIT') || $session->renewal || true) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="charge-fees-no">
              Ignore missed fees
            </label>
          </div>
        </div>

        <div class="collapse <?php if ($session->chargeOutstanding) { ?>show<?php } ?>" id="pro-rata-box">
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="charge-pro-rata" id="charge-pro-rata-yes" <?php if ($session->chargeProRata) { ?>checked<?php } ?> <?php if (!app()->tenant->getBooleanKey('USE_DIRECT_DEBIT')) { ?>disabled<?php } ?>>
              <label class="form-check-label" for="charge-pro-rata-yes">
                Charge pro-rata amount
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="charge-pro-rata" id="charge-pro-rata-no" <?php if (!$session->chargeProRata) { ?>checked<?php } ?> <?php if (!app()->tenant->getBooleanKey('USE_DIRECT_DEBIT')) { ?>disabled<?php } ?>>
              <label class="form-check-label" for="charge-pro-rata-no">
                Charge for full months
              </label>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label for="welcome-text" class="form-label">Welcome Text</label>
          <textarea class="form-control" id="welcome-text" name="welcome-text" rows="5" <?php if ($session->renewal) { ?>disabled<?php } ?>><?= htmlspecialchars($session->welcomeText) ?></textarea>
        </div>

        <div class="mb-3">
          <label for="status" class="form-label">Onboarding status</label>
          <select class="form-select" name="status" id="status" <?php if ($session->status == 'not_ready' || $session->renewal) { ?>disabled<?php } ?>>
            <?php foreach ($states as $key => $value) { ?>
              <option <?php if ($session->status == $key) { ?>selected<?php } ?> value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($value) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="has-due-date" id="has-due-date-yes" <?php if ($session->dueDate) { ?>checked<?php } ?> value="1" data-toggle="due-date-box" <?php if ($session->renewal) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="has-due-date-yes">
              Set due date
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="has-due-date" id="has-due-date-no" <?php if (!$session->dueDate) { ?>checked<?php } ?> value="0" data-toggle="due-date-box" <?php if ($session->renewal) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="has-due-date-no">
              Open ended
            </label>
          </div>
        </div>

        <div class="collapse <?php if ($session->dueDate) { ?>show<?php } ?>" id="due-date-box">
          <div class="mb-3">
            <label for="due-date" class="form-label">Due Date</label>
            <input type="date" class="form-control" id="due-date" name="due-date" value="<?php if ($session->dueDate) { ?><?= htmlspecialchars($session->dueDate->format('Y-m-d')) ?><?php } ?>" <?php if ($session->renewal) { ?>disabled<?php } ?>>
          </div>
        </div>

        <div class="mb-3">
          <p class="mb-2">
            Required tasks
          </p>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-account_details" name="task-account_details" <?php if ($session->stages->account_details->required) { ?>checked<?php } ?> <?php if ($session->stages->account_details->completed || $session->stages->account_details->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-account_details">
              Account details
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-address_details" name="task-address_details" <?php if ($session->stages->address_details->required) { ?>checked<?php } ?> <?php if ($session->stages->address_details->completed || $session->stages->address_details->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-address_details">
              Address details
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-communications_options" name="task-communications_options" <?php if ($session->stages->communications_options->required) { ?>checked<?php } ?> <?php if ($session->stages->communications_options->completed || $session->stages->communications_options->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-communications_options">
              Communications options
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-emergency_contacts" name="task-emergency_contacts" <?php if ($session->stages->emergency_contacts->required) { ?>checked<?php } ?> <?php if ($session->stages->emergency_contacts->completed || $session->stages->emergency_contacts->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-emergency_contacts">
              Emergency contacts
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-parent_conduct" name="task-parent_conduct" <?php if ($session->stages->parent_conduct->required) { ?>checked<?php } ?> <?php if ($session->stages->parent_conduct->completed || $session->stages->parent_conduct->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-parent_conduct">
              Parent/guardian code of conduct
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-data_privacy_agreement" name="task-data_privacy_agreement" <?php if ($session->stages->data_privacy_agreement->required) { ?>checked<?php } ?> <?php if ($session->stages->data_privacy_agreement->completed || $session->stages->data_privacy_agreement->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-data_privacy_agreement">
              Data privacy agreement
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-terms_agreement" name="task-terms_agreement" <?php if ($session->stages->terms_agreement->required) { ?>checked<?php } ?> <?php if ($session->stages->terms_agreement->completed || $session->stages->terms_agreement->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-terms_agreement">
              Terms and conditions of club membership agreement
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-direct_debit_mandate" name="task-direct_debit_mandate" <?php if ($session->stages->direct_debit_mandate->required) { ?>checked<?php } ?> <?php if ($session->stages->direct_debit_mandate->completed || $session->stages->direct_debit_mandate->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-direct_debit_mandate">
              Direct debit mandate
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="task-fees" name="task-fees" <?php if ($session->stages->fees->required) { ?>checked<?php } ?> <?php if ($session->stages->fees->completed || $session->stages->fees->required_locked) { ?>disabled<?php } ?>>
            <label class="form-check-label" for="task-fees">
              Registration fees
            </label>
          </div>

          <p>
            Some tasks may be locked if the system requires them or you do not have access to a given service. Other tasks may be locked if the user has started onboarding and completed a given task.
          </p>
        </div>

        <?php if ($membershipYear && $session->status == 'not_ready' && !$session->batch) { ?>
          <div class="mb-3">
            <label for="year" class="form-label">Membership year</label>
            <select class="form-select" name="year" id="year" required>
              <option selected value="" disabled="true">Select a membership year</option>
              <?php do { ?>
                <option value="<?= htmlspecialchars($membershipYear->id) ?>"><?= htmlspecialchars($membershipYear->name) ?></option>
              <?php } while ($membershipYear = $getMembershipYears->fetch(PDO::FETCH_OBJ)) ?>
            </select>
          </div>
        <?php } ?>

        <p class="mb-2">
          Onboarding members
        </p>

        <ul class="mb-3">
          <?php foreach ($session->members as $member) { ?>
            <li><?= htmlspecialchars($member->firstName . ' ' . $member->lastName) ?></li>
          <?php } ?>
        </ul>

        <p class="mb-2">
          Registration fees
        </p>
        <?php if ($session->status != 'complete' && $session->batch) { ?>
          <p>
            <a href="<?= htmlspecialchars(autoUrl("memberships/batches/" . $session->batch . "/edit")) ?>" class="btn btn-success">Edit fees</a>
          </p>
        <?php } else if ($session->status != 'complete') { ?>
          <p>
            <button type="submit" name="action" value="fees" class="btn btn-success">Go to fees</button>
          </p>
        <?php } ?>

        <?php if ($session->status == 'not_ready' && $session->batch) { ?>

          <p>
            All ready?
          </p>

          <p>
            <button type="submit" name="action" value="send" class="btn btn-success">Send registration email</button>
          </p>

        <?php } else { ?>

          <p>
            <button type="submit" class="btn btn-success">
              Update
            </button>
          </p>

        <?php } ?>
      </form>

    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('js/NeedsValidation.js');
$footer->addJs('js/onboarding/admin/session.js');
$footer->render();
