<?php

/**
 * New single member view and edit page
 */

$db = app()->db;

try {
  $member = new Member($id);
} catch (Exception $e) {
  halt(404);
}

$user = $member->getUser();

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' && (!$user || $user->getId() != $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) {
  halt(404);
}

$manageSquads = false;
if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Coach') {
  $manageSquads = true;
}

$squads = $member->getSquads();

$pagetitle = htmlspecialchars($member->getFullName());

$pageHead = [
  'body' => [
    'data-spy="scroll"',
    'data-target="#member-page-menu"'
  ]
];

$pbs = null;
// try {
//   $httpClient = new GuzzleHttp\Client();
//   $res = $httpClient->request('GET', 'https://dev.myswimmingclub.uk/bsdbc/members/' . $member->getSwimEnglandNumber() . '/times', []);
//   if ($res->getStatusCode() == "200") {
//     $pbs = json_decode($res->getBody());
//   }
// } catch (GuzzleHttp\Exception\ClientException | GuzzleHttp\Exception\ServerException $e) {
//   // 404 or something
// }

$extraFees = $db->prepare("SELECT extras.ExtraID, ExtraName, ExtraFee, `Type` FROM extrasRelations INNER JOIN extras ON extras.ExtraID = extrasRelations.ExtraID WHERE extrasRelations.MemberID = ?");
$extraFees->execute([
  $id,
]);

$getLatestCovidSurveyCompletion = $db->prepare("SELECT `ID`, `DateTime`, `OfficerApproval`, `ApprovedBy`, `Forename`, `Surname` FROM covidHealthScreen LEFT JOIN users ON covidHealthScreen.ApprovedBy = users.UserID WHERE Member = ? ORDER BY `DateTime` DESC LIMIT 1");
$getLatestCovidSurveyCompletion->execute([
  $id
]);
$latestCovidSurvey = $getLatestCovidSurveyCompletion->fetch(PDO::FETCH_ASSOC);

$getLatestCovidRAForm = $db->prepare("SELECT `ID`, `DateTime`, `MemberAgreement`, `Guardian`, `Forename`, `Surname` FROM covidRiskAwareness LEFT JOIN users ON users.UserID = covidRiskAwareness.Guardian WHERE Member = ? ORDER BY `DateTime` DESC LIMIT 1");
$getLatestCovidRAForm->execute([
  $id
]);
$latestCovidRAForm = $getLatestCovidRAForm->fetch(PDO::FETCH_ASSOC);

$fluidContainer = true;
include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-fluid">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= autoUrl("members") ?>">Members</a></li>
        <li class="breadcrumb-item active" aria-current="page">#<?= htmlspecialchars($member->getId()) ?></li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($member->getFullName()) ?>
        </h1>
        <p class="lead mb-0" id="leadDesc">
          Member
        </p>
        <div class="mb-3 d-lg-none"></div>
      </div>
      <div class="ml-auto col-lg-auto">
        <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') { ?>
          <p>
            <a href="<?= htmlspecialchars(autoUrl("members/" . $id . "/edit")) ?>" class="btn btn-block btn-success">
              Edit basic details
            </a>
          </p>
        <?php } ?>
        <?php if ($user && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Parent') { ?>
          <p>
            <div class="dropdown">
              <button class="btn btn-secondary btn-block dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Quick actions
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("members/" . $id . "/enter-gala")) ?>">Enter a gala</a>
                <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("users/" . $user->getId())) ?>">View linked user</a>
                <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("members/" . $id . "/contact-parent")) ?>">Email user/parent/guardian</a>
                <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Galas') { ?>
                  <button class="dropdown-item" id="new-move-dropdown" type="button">New squad move</button>
                  <a class="dropdown-item" href="<?= htmlspecialchars(autoUrl("members/" . $id . "/parenthelp")) ?>">Print access key</a>
                <?php } ?>
              </div>
            </div>
          </p>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">

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
          <a href="#extras" class="list-group-item list-group-item-action">
            Extra fees
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
            <?= htmlspecialchars($member->getDateOfBirth()->format("j F Y")) ?>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Country
          </dt>
          <dd>
            <?= htmlspecialchars($member->getCountry()) ?>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Swim England #
          </dt>
          <dd>
            <a href="<?= htmlspecialchars('https://www.swimmingresults.org/biogs/biogs_details.php?tiref=' . $member->getSwimEnglandNumber()) ?>">
              <?= htmlspecialchars($member->getSwimEnglandNumber()) ?>
            </a>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Membership category
          </dt>
          <dd>
            <?= htmlspecialchars($member->getSwimEnglandCategory()) ?>
          </dd>
        </div>
      </dl>

      <!-- <p>
        <button class="btn btn-success">
          Edit basic details
        </button>
      </p> -->

      <hr>

      <h2 id="covid-screen">COVID-19 Health Survey</h2>

      <?php if ($latestCovidSurvey) {
        $time = new DateTime($latestCovidSurvey['DateTime'], new DateTimeZone('UTC'));
        $time->setTimezone(new DateTimeZone('Europe/London'));
      ?>
        <p>
          Latest submission <?= htmlspecialchars($time->format('H:i, j F Y')) ?><br>
          <?php if (bool($latestCovidSurvey['OfficerApproval'])) { ?>
            <span class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Approved by <?= htmlspecialchars($latestCovidSurvey['Forename'] . ' ' . $latestCovidSurvey['Surname']) ?></span>
          <?php } else if (!bool($latestCovidSurvey['OfficerApproval']) && $latestCovidSurvey['ApprovedBy']) { ?>
            <span class="text-danger"><i class="fa fa-times-circle" aria-hidden="true"></i> Rejected by <?= htmlspecialchars($latestCovidSurvey['Forename'] . ' ' . $latestCovidSurvey['Surname']) ?></span>
          <?php } else if (!bool($latestCovidSurvey['OfficerApproval'])) { ?>
            <span class="text-warning"><i class="fa fa-minus-circle" aria-hidden="true"></i> Awaiting approval</span>
          <?php } ?>
        </p>

        <p>
          <a href="<?= htmlspecialchars(autoUrl('covid/health-screening/members/' . $id)) ?>" class="btn btn-success">
            View all submissions
          </a>
        </p>
      <?php } else { ?>
        <p class="mb-0">
          No survey submitted
        </p>
      <?php } ?>

      <hr>

      <h2 id="covid-risk-awareness">COVID-19 Risk Awareness Declaration</h2>

      <?php if ($latestCovidRAForm) {
        $time = new DateTime($latestCovidRAForm['DateTime'], new DateTimeZone('UTC'));
        $time->setTimezone(new DateTimeZone('Europe/London'));
      ?>
        <?php if (bool($latestCovidRAForm['MemberAgreement'])) { ?>
          <span class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Signed at <?= htmlspecialchars($time->format('H:i, j F Y')) ?><?php if ($latestCovidRAForm['Guardian']) { ?> with <?= htmlspecialchars($latestCovidRAForm['Forename'] . ' ' . $latestCovidRAForm['Surname']) ?> as parent/guardian<?php } ?></span>
        <?php } else { ?>
          <span class="text-warning"><i class="fa fa-minus-circle" aria-hidden="true"></i> A new declaration form is required</span>
        <?php } ?>
      <?php } else { ?>
        <span class="text-danger"><i class="fa fa-times-circle" aria-hidden="true"></i> No risk awareness declaration submitted</span>
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
            <div class="cell mt-1 mb-0"><?= $medical->getConditions() ?></div>
          </dd>

          <dt class="text-truncate">
            Allergies
          </dt>
          <dd>
            <div class="cell mt-1 mb-0"><?= $medical->getAllergies() ?></div>
          </dd>

          <dt class="text-truncate">
            Medication
          </dt>
          <dd>
            <div class="cell mt-1 mb-0"><?= $medical->getMedication() ?></div>
          </dd>
        </dl>

      <?php } else { ?>

        <p>
          <?= htmlspecialchars($member->getForename()) ?> does not have any medical notes to display.
        </p>

      <?php } ?>

      <!-- <p>
        <button class="btn btn-success">
          Edit medical notes
        </button>
      </p> -->

      <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' || $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') { ?>
        <p>
          <a href="<?= htmlspecialchars(autoUrl("members/" . $id . "/medical")) ?>" class="btn btn-success">
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
                  <div class="col-sm-6 col-md-12 col-lg-6">
                    <div class="text-truncate"><strong><?= htmlspecialchars($ec->getName()) ?></strong></div>
                    <div class="text-truncate"><?= htmlspecialchars($ec->getRelation()) ?></div>
                    <div class="mb-2 d-sm-none d-md-flex d-lg-none"></div>
                  </div>
                  <div class="col">
                    <a href="<?= htmlspecialchars($ec->getRFCContactNumber()) ?>" class="btn btn-block btn-success">
                      <i class="fa fa-phone" aria-hidden="true"></i> <?= htmlspecialchars($ec->getNationalContactNumber()) ?>
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
            <?= htmlspecialchars($member->getForename()) ?> should not be allowed to train until details have been provided.
          </p>
        </div>

      <?php } ?>

      <hr>

      <!-- Photo permissions -->
      <h2 id="photography-permissions">Photography permissions</h2>
      <?php if ($member->getAge() >= 18) { ?>

        <p>
          <?= htmlspecialchars($member->getForename()) ?> is <?= htmlspecialchars($member->getAge()) ?> so has no photography restrictions in place.
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
                    <li><?= htmlspecialchars($text->getDescription()) ?></li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          <?php } ?>

          <?php if (sizeof($perms['allowed']) > 0 && sizeof($perms['disallowed']) > 0) { ?>
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
                    <li><?= htmlspecialchars($text->getDescription()) ?></li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          <?php } ?>
        </div>

        <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') { ?>
          <p class="mt-3">
            <a href="<?= htmlspecialchars(autoUrl("members/" . $id . "/edit")) ?>" class="btn btn-success">
              Edit photography preferences
            </a>
          </p>
        <?php } ?>

      <?php } ?>

      <hr>

      <!-- Squad details -->
      <h2 id="squads">Squad<?php if (sizeof($squads) != 1) { ?>s<?php } ?></h2>
      <div id="squadDetails">
        <!-- <p>
        <?= htmlspecialchars($member->getForename()) ?> is a member of <?= htmlspecialchars((new NumberFormatter("en", NumberFormatter::SPELLOUT))->format(sizeof($squads))) ?><?php if (sizeof($squads) != 1) { ?>s<?php } ?>.
      </p>

      <div class="list-group mb-3">
        <?php foreach ($squads as $squad) { ?>
          <a href="<?= htmlspecialchars(autoUrl('squads/' . $squad->getId())) ?>" class="list-group-item list-group-item-action">
            <?= htmlspecialchars($squad->getName()) ?>
          </a>
        <?php } ?>
      </div> -->
      </div>

      <?php if ($manageSquads) { ?>
        <p>
          <button class="btn btn-success" id="new-move-button" data-member="<?= htmlspecialchars($id) ?>" data-squads-url="<?= htmlspecialchars(autoUrl("members/$id/squads.json")) ?>" data-move-url="<?= htmlspecialchars(autoUrl("members/move-squad")) ?>" data-csrf="<?= htmlspecialchars(\SCDS\CSRF::getValue()) ?>">
            Manage squads
          </button>
        </p>

        <p>
          To change whether <?= htmlspecialchars($member->getForename()) ?> pays for a squad, remove them and add them again. The ability to modify this setting properly is coming soon.
        </p>
      <?php } ?>
      <div id="squads-data" data-squads-url="<?= htmlspecialchars(autoUrl("members/$id/squads.json")) ?>"></div>

      <div id="squad-moves-area" data-show-options="<?php if ($manageSquads) { ?>true<?php } else { ?>false<?php } ?>" data-operations-url="<?= htmlspecialchars(autoUrl('members/move-operations')) ?>"></div>

      <hr>

      <h2 id="extras">Extra Fees</h2>

      <?php if ($extra = $extraFees->fetch(PDO::FETCH_ASSOC)) { ?>
        <div class="list-group">
          <?php do { ?>
            <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') { ?>
              <div class="list-group-item">
                <?= htmlspecialchars($extra['ExtraName']) ?> <em><?php if ($extra['Type'] == 'Refund') { ?>(Credit) <?php } ?>&pound;<?= (string) \Brick\Math\BigDecimal::of((string) $extra['ExtraFee'])->toScale(2) ?>/month</em>
              </div>
            <?php } else { ?>
              <a href="<?= htmlspecialchars(autoUrl('payments/extrafees/' . $extra['ExtraID'])) ?>" class="list-group-item list-group-item-action">
                <?= htmlspecialchars($extra['ExtraName']) ?> <em><?php if ($extra['Type'] == 'Refund') { ?>(Credit) <?php } ?>&pound;<?= (string) \Brick\Math\BigDecimal::of((string) $extra['ExtraFee'])->toScale(2) ?>/month</em>
              </a>
            <?php } ?>
          <?php } while ($extra = $extraFees->fetch(PDO::FETCH_ASSOC)); ?>
        </div>
      <?php } else { ?>
        <p>
          <?= htmlspecialchars($member->getForename()) ?> has no additional fees to pay.
        </p>
      <?php } ?>

      <hr>

      <!-- Times -->
      <h2 id="personal-bests">Personal bests</h2>
      <p>
        View <?= htmlspecialchars($member->getForename()) ?>'s personal best times.
      </p>

      <p>
        <a href="<?= htmlspecialchars(autoUrl("members/" . $id . "/times")) ?>" class="btn btn-success">
          See personal bests
        </a>
      </p>

      <?php if ($pbs) { ?>
        <h3><span class="badge badge-info">BETA</span> PBs <small class="text-muted">direct from British Swimming</small></h3>

        <div class="row">
          <div class="col-sm-6">
            <h4>Long Course</h4>
            <ul class="list-unstyled">
              <?php foreach ($pbs->long_course as $eventCode => $event) {
                $swim = $event->swims[0]; ?>
                <li><strong><?= htmlspecialchars($event->event_name) ?></strong><br><?= htmlspecialchars($swim->time) ?> - <?= htmlspecialchars($swim->meet->name) ?> on <?= htmlspecialchars((new DateTime($swim->swim_date, new DateTimeZone('Europe/London')))->format("j F Y")) ?></li>
              <?php } ?>
            </ul>
          </div>
          <div class="col-sm-6">
            <h4>Short Course</h4>
            <ul class="list-unstyled">
              <?php foreach ($pbs->short_course as $eventCode => $event) {
                $swim = $event->swims[0]; ?>
                <li><strong><?= htmlspecialchars($event->event_name) ?></strong><br><?= htmlspecialchars($swim->time) ?> - <?= htmlspecialchars($swim->meet->name) ?> on <?= htmlspecialchars((new DateTime($swim->swim_date, new DateTimeZone('Europe/London')))->format("j F Y")) ?></li>
              <?php } ?>
            </ul>
          </div>
        </div>

      <?php } ?>

      <hr>

      <!-- Membership administration details -->
      <h2 id="membership-details">Membership details</h2>

      <h3>Club Membership</h3>
      <dl class="row">
        <div class="col-6">
          <dt class="text-truncate">
            Club membership category
          </dt>
          <dd>
            <?= htmlspecialchars($member->getClubCategory()) ?>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Club pays club membership fees
          </dt>
          <dd>
            <?php if ($member->clubMembershipPaid()) { ?>Yes<?php } else { ?>No, member pays<?php } ?>
          </dd>
        </div>
      </dl>

      <h3>Swim England Membership</h3>
      <dl class="row">
        <div class="col-6">
          <dt class="text-truncate">
            Swim England membership category
          </dt>
          <dd>
            <?= htmlspecialchars($member->getSwimEnglandCategory()) ?>
          </dd>
        </div>

        <div class="col-6">
          <dt class="text-truncate">
            Club pays Swim England fees
          </dt>
          <dd>
            <?php if ($member->swimEnglandFeesPaid()) { ?>Yes<?php } else { ?>No, member pays<?php } ?>
          </dd>
        </div>


        </dl>

        <hr>

        <!-- Other notes -->
        <h2 id="other-details">Other details</h2>
        <?php $md = $member->getNotes(); ?>
        <?php if ($md) { ?>
          <?= $md ?>
        <?php } else { ?>
          <p>
            No additional notes for <?= htmlspecialchars($member->getForename()) ?>
          </p>
        <?php } ?>

      </div>
    </div>

  </div>

  <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div id="modal-body" class="modal-body"></div>
        <div id="modal-footer" class="modal-footer">
          <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <?php

  $footer = new \SCDS\Footer();
  $footer->addJs('public/js/members/main.js');
  $footer->useFluidContainer();
  $footer->render();
