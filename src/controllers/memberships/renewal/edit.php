<?php

if (!app()->user->hasPermission('Admin')) halt(404);

$db = app()->db;
$tenant = app()->tenant;

// Get membership years
$today = new DateTime('now', new DateTimeZone('Europe/London'));

$renewal = \SCDS\Onboarding\Renewal::retrieve($id);

if (!$renewal) halt(404);

// $startDate = new DateTime($renewal->start, new DateTimeZone('Europe/London'));
// $endDate = new DateTime($renewal->end, new DateTimeZone('Europe/London'));
// $yearStart = new DateTime($renewal->yearStart, new DateTimeZone('Europe/London'));
// $yearEnd = new DateTime($renewal->yearEnd, new DateTimeZone('Europe/London'));

$stages = $renewal->defaultStages;
$stageNames = SCDS\Onboarding\Session::stagesOrder();
$memberStages = $renewal->defaultMemberStages;
$memberStageNames = SCDS\Onboarding\Member::stagesOrder();

$started = $renewal->isCurrent() || $renewal->isPast() || $renewal->isCreated();

$pagetitle = "Edit - " . htmlspecialchars($renewal->start->format('j M Y') . " - " . $renewal->end->format('j M Y')) . " - Renewal - Membership Centre";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("memberships")) ?>">Memberships</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("memberships/renewal")) ?>">Renewal</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("memberships/renewal/$id")) ?>"><?= htmlspecialchars($renewal->start->format('d/m/Y')) ?> - <?= htmlspecialchars($renewal->end->format('d/m/Y')) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($renewal->start->format('j M Y')) ?> - <?= htmlspecialchars($renewal->end->format('j M Y')) ?> Renewal
        </h1>
        <p class="lead mb-0">
          For the year <?= htmlspecialchars($renewal->year->start->format('j M Y')) ?> - <?= htmlspecialchars($renewal->year->end->format('j M Y')) ?> (<?= htmlspecialchars($renewal->year->name) ?>)
        </p>
      </div>
      <div class="col text-lg-end">
        <p class="mb-0">
          <a href="<?= htmlspecialchars(autoUrl("memberships/renewal/$id/edit")) ?>" class="btn btn-success">Edit</a>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row">
    <div class="col-lg-8">
      <main>

        <form class="needs-validation" method="post" novalidate>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="start" class="form-label">Renewal period start date</label>
                <input type="date" class="form-control" id="start" name="start" placeholder="<?= htmlspecialchars($renewal->start->format('Y-m-d')) ?>" value="<?= htmlspecialchars($renewal->start->format('Y-m-d')) ?>" required>
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label for="end" class="form-label">Renewal period end date</label>
                <input type="date" class="form-control" id="end" name="end" placeholder="<?= htmlspecialchars($renewal->start->format('Y-m-d')) ?>" value="<?= htmlspecialchars($renewal->start->format('Y-m-d')) ?>" required>
              </div>
            </div>
          </div>

          <p>
            The renewal period start and end dates should define the period in which people can complete membership renewal.
          </p>

          <?php if ($started) { ?>
            <div class="alert alert-warning">
              <p class="mb-0">
                <strong>This renewal period has started, therefore you can not edit the required stages</strong>
              </p>
              <p class="mb-0">
                You can edit the required stages for an individual user by finding their onboarding session.
              </p>
            </div>
          <?php } ?>

          <p>
            Required stages
          </p>

          <div class="mb-3">
            <?php foreach ($stageNames as $stage => $desc) { ?>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="<?= htmlspecialchars($stage . '-main-check') ?>" name="<?= htmlspecialchars($stage . '-main-check') ?>" <?php if ($stages->$stage->required) { ?>checked<?php } ?> <?php if ($stages->$stage->required_locked || $started) { ?>disabled<?php } ?>>
                <label class="form-check-label" for="<?= htmlspecialchars($stage . '-main-check') ?>">
                  <?= htmlspecialchars($desc) ?>
                </label>
              </div>
            <?php } ?>
          </div>

          <p>
            Member information stage includes the following;
          </p>

          <div class="mb-3">
            <?php foreach ($memberStageNames as $stage => $desc) { ?>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="<?= htmlspecialchars($stage . '-member-check') ?>" name="<?= htmlspecialchars($stage . '-member-check') ?>" <?php if ($memberStages->$stage->required) { ?>checked<?php } ?> <?php if ($memberStages->$stage->required_locked || $started) { ?>disabled<?php } ?>>
                <label class="form-check-label" for="<?= htmlspecialchars($stage . '-member-check') ?>">
                  <?= htmlspecialchars($desc) ?>
                </label>
              </div>
            <?php } ?>
          </div>

          <p>
            Photography consents will only be asked from members who are aged under 18 when renewal opens.
          </p>

          <?= \SCDS\CSRF::write(); ?>

          <p>
            <button type="submit" class="btn btn-success">
              Save
            </button>
          </p>
        </form>

      </main>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('js/NeedsValidation.js');
$footer->render();
