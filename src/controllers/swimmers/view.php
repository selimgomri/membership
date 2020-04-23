<?php

/**
 * New single member view and edit page
 */

try {
  $member = new Member($id);
} catch (Exception $e) {
  halt(404);
}

$user = $member->getUser();

if ($_SESSION['AccessLevel'] == 'Parent' && (!$user || $user->getId() != $_SESSION['UserID'])) {
  halt(404);
}

$squads = $member->getSquads();

$pagetitle = htmlspecialchars($member->getFullName());

$fluidContainer = true;
include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">

  <!-- Page header -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("members")?>">Members</a></li>
      <li class="breadcrumb-item active" aria-current="page">#<?=htmlspecialchars($member->getId())?></li>
    </ol>
  </nav>

  <h1>
    <?=htmlspecialchars($member->getFullName())?>
  </h1>
  <p class="lead">
    <?php if (sizeof($squads) > 0) { ?><?php for ($i=0; $i < sizeof($squads); $i++) { ?><?=htmlspecialchars($squads[$i]->getName())?><?php if ($i < sizeof($squads)-1) { ?>, <?php } ?><?php } ?> Squad<?php if (sizeof($squads) != 1) { ?>s<?php } ?><?php } else { ?>Not assigned to any squads<?php } ?>
  </p>

  <div class="row justify-content-between">
    <div class="col-md-4 col-lg-3 col-xl-3">
      <div class="card mb-3">
        <div class="card-header">
          Jump to
        </div>
        <div class="list-group">
          <a href="#basic-information" class="list-group-item list-group-item-action">
            Basic information
          </a>
          <a href="#medical-details" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            Medical notes
            <i class="fa fa-fw fa-ambulance" aria-hidden="true"></i>
          </a>
          <a href="#emergency-contacts" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            Emergency contacts
            <i class="fa fa-fw fa-phone" aria-hidden="true"></i>
          </a>
          <a href="#squads" class="list-group-item list-group-item-action">
            Squad<?php if (sizeof($squads) != 1) { ?>s<?php } ?>
          </a>
          <a href="#membership-details" class="list-group-item list-group-item-action">
            Membership details
          </a>
          <a href="#other-details" class="list-group-item list-group-item-action">
            Other details
          </a>
        </div>
      </div>
    </div>

    <div class="col">
      <!-- Basic information -->
      <h2 id="basic-information">Basic information</h2>
      <dl class="row">
        <dt class="col-12">
          Date of birth
        </dt>
        <dd class="col-12">
          <?=htmlspecialchars($member->getDateOfBirth()->format("j F Y"))?>
        </dd>

        <div class="col-6">
          <dt class="text-truncate">
            Swim England #
          </dt>
          <dd>
            <a href="<?=htmlspecialchars('https://www.swimmingresults.org/biogs/biogs_details.php?tiref=' . $member->getSwimEnglandNumber())?>">
              <?=htmlspecialchars($member->getSwimEnglandNumber())?>
            </a>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Membership category
          </dt>
          <dd>
            <?=htmlspecialchars($member->getSwimEnglandCategory())?>
          </dd>
        </div>
      </dl>

      <p>
        <button class="btn btn-success">
          Edit basic details
        </button>
      </p>

      <!-- Medical details -->
      <h2 id="medical-details">Medical notes</h2>

      <?php $medical = $member->getMedicalNotes(); ?>
      <?php if ($medical->hasMedicalNotes()) { ?>

      <dl>
        <dt class="text-truncate">
          Medical Conditions or Disabilities
        </dt>
        <dd>
          <div class="cell mt-1 mb-0"><?=$medical->getConditions()?></div>
        </dd>

        <dt class="text-truncate">
          Allergies
        </dt>
        <dd>
          <div class="cell mt-1 mb-0"><?=$medical->getAllergies()?></div>
        </dd>

        <dt class="text-truncate">
          Medication
        </dt>
        <dd>
          <div class="cell mt-1 mb-0"><?=$medical->getMedication()?></div>
        </dd>
      </dl>

      <?php } else { ?>

      <p>
        <?=htmlspecialchars($member->getForename())?> does not have any medical notes to display.
      </p>

      <?php } ?>

      <p>
        <button class="btn btn-success">
          Edit medical notes
        </button>
      </p>

      <!-- Emergency details -->
      <h2 id="emergency-contacts">Emergency contact details</h2>
      
      <?php $emergencyContacts = $member->getEmergencyContacts(); ?>

      <div class="row">
        <?php foreach ($emergencyContacts as $ec) { ?>
        
        <div class="col-md-6 col-lg-4">
          <div class="cell p-2 mb-2">
            <div class="row align-items-center">
              <div class="col-6">
                <strong><?=htmlspecialchars($ec->getName())?></strong><br>
                <?=htmlspecialchars($ec->getRelation())?>
              </div>
              <div class="col-6">
                <a href="<?=htmlspecialchars($ec->getRFCContactNumber())?>" class="btn btn-block btn-success">
                  <i class="fa fa-phone" aria-hidden="true"></i> <?=htmlspecialchars($ec->getNationalContactNumber())?>
                </a>
              </div>
            </div>
          </div>
        </div>

        <?php } ?>
      </div>

      <!-- Squad details -->
      <h2 id="squads">Squad<?php if (sizeof($squads) != 1) { ?>s<?php } ?></h2>
      <p>
        <?=htmlspecialchars($member->getForename())?> is a member of <?=htmlspecialchars((new NumberFormatter("en", NumberFormatter::SPELLOUT))->format(sizeof($squads)))?> squad<?php if (sizeof($squads) != 1) { ?>s<?php } ?>.
      </p>

      <div class="list-group mb-3">
        <?php foreach ($squads as $squad) { ?>
          <a href="<?=htmlspecialchars(autoUrl('squads/' . $squad->getId()))?>" class="list-group-item list-group-item-action">
            <?=htmlspecialchars($squad->getName())?>
          </a>
        <?php } ?>
      </div>

      <p>
        <button class="btn btn-success">
          Manage squads
        </button>
      </p>

      <!-- Membership administration details -->
      <h2 id="membership-details">Membership details</h2>

      <!-- Other notes -->
      <h2 id="other-details">Other details</h2>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();