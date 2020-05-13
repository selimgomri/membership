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

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' && (!$user || $user->getId() != $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) {
  halt(404);
}

$squads = $member->getSquads();

$pagetitle = htmlspecialchars($member->getFullName());

$pageHead = [
  'body' => [
    'data-spy="scroll"',
    'data-target="#member-page-menu"'
  ]
];

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

  <?php if ($user && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Parent') { ?>
  <p>
    <div class="dropdown">
      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Quick actions
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="<?=htmlspecialchars(autoUrl("members/" . $id . "/enter-gala"))?>">Enter a gala</a>
        <a class="dropdown-item" href="<?=htmlspecialchars(autoUrl("members/" . $id . "/contact-parent"))?>">Email user/parent/guardian</a>
        <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Galas') { ?>
        <a class="dropdown-item" href="<?=htmlspecialchars(autoUrl("members/" . $id . "/new-move"))?>">New squad move</a>
        <a class="dropdown-item" href="<?=htmlspecialchars(autoUrl("members/" . $id . "/parenthelp"))?>">Print access key</a>
        <?php } ?>
      </div>
    </div>
  </p>
  <?php } ?>

  <div class="row justify-content-between">
    <div class="col-md-4 col-lg-3 col-xl-3">
      <div class="position-sticky top-3 card mb-3">
        <div class="card-header">
          Jump to
        </div>
        <div class="list-group list-group-flush" id="member-page-menu">
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
          <a href="#photography-permissions" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            Photography consent
            <i class="fa fa-fw fa-camera" aria-hidden="true"></i>
          </a>
          <a href="#squads" class="list-group-item list-group-item-action">
            Squad<?php if (sizeof($squads) != 1) { ?>s<?php } ?>
          </a>
          <a href="#personal-bests" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            Personal best times
            <i class="fa fa-fw fa-clock-o" aria-hidden="true"></i>
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
        <div class="col-6">
          <dt class="text-truncate">
            Date of birth
          </dt>
          <dd>
            <?=htmlspecialchars($member->getDateOfBirth()->format("j F Y"))?>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Country
          </dt>
          <dd>
            <?=htmlspecialchars($member->getCountry())?>
          </dd>
        </div>

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

      <!-- <p>
        <button class="btn btn-success">
          Edit basic details
        </button>
      </p> -->

      <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') { ?>
      <p>
        <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/edit"))?>" class="btn btn-success">
          Edit basic details
        </a>
      </p>
      <?php } ?>

      <hr>

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

      <!-- <p>
        <button class="btn btn-success">
          Edit medical notes
        </button>
      </p> -->

      <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') { ?>
      <p>
        <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/medical"))?>" class="btn btn-success">
          Edit medical notes
        </a>
      </p>
      <?php } ?>

      <hr>

      <!-- Emergency details -->
      <h2 id="emergency-contacts">Emergency contact details</h2>
      
      <?php $emergencyContacts = $member->getEmergencyContacts(); ?>

      <?php if ($emergencyContacts) { ?>

      <p>
        In an emergency, dial one of the contact numbers shown below.
      </p>

      <div class="row">
        <?php foreach ($emergencyContacts as $ec) { ?>
        
        <div class="col-md-6 col-xl-4">
          <div class="card card-body py-2 px-3 mb-2">
            <div class="row align-items-center">
              <div class="col-6">
                <div class="text-truncate"><strong><?=htmlspecialchars($ec->getName())?></strong></div>
                <div class="text-truncate"><?=htmlspecialchars($ec->getRelation())?></div>
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

      <?php } else { ?>

      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>No emergency contact details are available.</strong>
        </p>
        <p class="mb-0">
          <?=htmlspecialchars($member->getForename())?> should not be allowed to train until details have been provided.
        </p>
      </div>

      <?php } ?>

      <hr>

      <!-- Photo permissions -->
      <h2 id="photography-permissions">Photography permissions</h2>
      <?php if ($member->getAge() >= 18) { ?>

      <p>
        <?=htmlspecialchars($member->getForename())?> is <?=htmlspecialchars($member->getAge())?> so has no photography restrictions in place.
      </p>

      <?php } else { ?>

      <p>
        Club staff are required to follow Swim England's Wavepower as well as relevant club guidance when taking photos or videos.
      </p>

      <div class="row d-flex align-items-stretch">
        <?php $perms = $member->getPhotoPermissions(); ?>

        <?php if (sizeof($perms['allowed']) > 0) { ?>
        <div class="col-xl-6">
          <div class="card card-body border-success h-100">
            <p class="text-success">
              <i class="fa fa-check-circle" aria-hidden="true"></i> <strong>You may</strong>
            </p>

            <ul class="list-unstyled mb-0">
              <?php foreach ($perms['allowed'] as $text) { ?>
              <li><?=htmlspecialchars($text->getDescription())?></li>
              <?php } ?>
            </ul>
          </div>
        </div>

        <div class="col-12 mb-2 d-block d-xl-none"></div>
        <?php } ?>

        <?php if (sizeof($perms['disallowed']) > 0) { ?>
        <div class="col-xl-6">
          <div class="card card-body border-danger h-100">
            <p class="text-danger">
              <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <strong>You must not</strong>
            </p>

            <ul class="list-unstyled mb-0">
              <?php foreach ($perms['disallowed'] as $text) { ?>
              <li><?=htmlspecialchars($text->getDescription())?></li>
              <?php } ?>
            </ul>
          </div>
        </div>
        <?php } ?>
      </div>

      <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') { ?>
      <p>
        <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/edit"))?>" class="btn btn-success">
          Edit photography preferences
        </a>
      </p>
      <?php } ?>

      <?php } ?>

      <hr>

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

      <!-- <p>
        <button class="btn btn-success">
          Manage squads
        </button>
      </p> -->

      <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Coach') { ?>
      <p>
        <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/new-move"))?>" class="btn btn-success">
          Manage squads
        </a>
      </p>
      <?php } ?>

      <hr>

      <!-- Times -->
      <h2 id="personal-bests">Personal bests</h2>
      <p>
        View <?=htmlspecialchars($member->getForename())?>'s personal best times.
      </p>

      <p>
        <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/times"))?>" class="btn btn-success">
          See personal bests
        </a>
      </p>

      <hr>

      <!-- Membership administration details -->
      <h2 id="membership-details">Membership details</h2>
      <div class="alert alert-info">
        <p class="mb-0">
          <strong>New Swim England and club membership management options will be coming soon for club staff.</strong>
        </p>
        <p class="mb-0">
          We'll also soon start displaying these.
        </p>
      </div>
      <dl class="row">
        <div class="col-6">
          <dt class="text-truncate">
            Club pays squad fees
          </dt>
          <dd>
            <?php if ($member->squadFeesPaid()) { ?>Yes<?php } else { ?>No, member pays<?php } ?>
          </dd>
        </div>

        <!-- <div class="col-6">
          <dt class="text-truncate">
            Club pays club membership fees
          </dt>
          <dd>
            <?php if ($member->clubMembershipPaid()) { ?>Yes<?php } else { ?>No, member pays<?php } ?>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Club pays Swim England fees
          </dt>
          <dd>
            <?php if ($member->swimEnglandFeesPaid()) { ?>Yes<?php } else { ?>No, member pays<?php } ?>
          </dd>
        </div> -->

        <div class="col-6">
          <dt class="text-truncate">
            Swim England Membership category
          </dt>
          <dd>
            <?=htmlspecialchars($member->getSwimEnglandCategory())?>
          </dd>
        </div>
      </dl>

      <hr>

      <!-- Other notes -->
      <h2 id="other-details">Other details</h2>
      <?php $md = $member->getNotes(); ?>
      <?php if ($md) { ?>
        <?=$md?>
      <?php } else { ?>
        <p>
          No additional notes for <?=htmlspecialchars($member->getForename())?>
        </p>
      <?php } ?>

    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();