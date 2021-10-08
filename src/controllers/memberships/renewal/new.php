<?php

if (!app()->user->hasPermission('Admin')) halt(404);

$db = app()->db;
$tenant = app()->tenant;

// Get membership years
$today = new DateTime('now', new DateTimeZone('Europe/London'));

$getYears = $db->prepare("SELECT `ID` `id`, `Name` `name`, `StartDate` `start`, `EndDate` `end` FROM `membershipYear` WHERE `Tenant` = ? AND `EndDate` >= ? ORDER BY `StartDate` ASC, `EndDate` ASC, `Name` ASC");
$getYears->execute([
  $tenant->getId(),
  $today->format('Y-m-d'),
]);
$year = $getYears->fetch(PDO::FETCH_OBJ);

$startDate = new DateTime('first day of January next year', new DateTimeZone('Europe/London'));
$endDate = new DateTime('last day of January next year', new DateTimeZone('Europe/London'));

$stages = SCDS\Onboarding\Session::getDefaultRenewalStages();
$stageNames = SCDS\Onboarding\Session::stagesOrder();
$memberStages = SCDS\Onboarding\Member::getDefaultStages();
$memberStageNames = SCDS\Onboarding\Member::stagesOrder();

$pagetitle = "New Renewal Period - Membership Centre";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships")) ?>">Memberships</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships/renewal")) ?>">Renewal</a></li>
        <li class="breadcrumb-item active" aria-current="page">New</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          New Renewal Period
        </h1>
        <p class="lead mb-0">
          Create a new renewal period
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row">
    <div class="col-lg-8">
      <main>
        <form class="needs-validation" novalidate method="post">

          <?php if ($year) { ?>

            <div class="mb-3">
              <label for="year-ngb" class="form-label">Membership year (for Swim England)</label>
              <select class="form-select" id="year-ngb" name="year-ngb" required>
                <option selected disabled>Choose a year</option>
                <option value="NONE">None</option>
                <?php do {
                  $start = new DateTime($year->start, new DateTimeZone('Europe/London'));
                  $end = new DateTime($year->end, new DateTimeZone('Europe/London'));
                ?>
                  <option value="<?= htmlspecialchars($year->id) ?>"><?= htmlspecialchars($year->name) ?> (<?= htmlspecialchars($start->format('j M Y')) ?> - <?= htmlspecialchars($end->format('j M Y')) ?>)</option>
                <?php } while ($year = $getYears->fetch(PDO::FETCH_OBJ)); ?>
              </select>
            </div>

            <?php

            // Fetch again
            $getYears->execute([
              $tenant->getId(),
              $today->format('Y-m-d'),
            ]);
            $year = $getYears->fetch(PDO::FETCH_OBJ);
            ?>

            <div class="mb-3">
              <label for="year-club" class="form-label">Membership year (for Club Membership)</label>
              <select class="form-select" id="year-club" name="year-club" required>
                <option selected disabled>Choose a year</option>
                <option value="NONE">None</option>
                <?php do {
                  $start = new DateTime($year->start, new DateTimeZone('Europe/London'));
                  $end = new DateTime($year->end, new DateTimeZone('Europe/London'));
                ?>
                  <option value="<?= htmlspecialchars($year->id) ?>"><?= htmlspecialchars($year->name) ?> (<?= htmlspecialchars($start->format('j M Y')) ?> - <?= htmlspecialchars($end->format('j M Y')) ?>)</option>
                <?php } while ($year = $getYears->fetch(PDO::FETCH_OBJ)); ?>
              </select>
            </div>

            <div class="row">
              <div class="col">
                <div class="mb-3">
                  <label for="start" class="form-label">Renewal period start date</label>
                  <input type="date" class="form-control" id="start" name="start" placeholder="<?= htmlspecialchars($startDate->format('Y-m-d')) ?>" value="<?= htmlspecialchars($startDate->format('Y-m-d')) ?>" required>
                </div>
              </div>
              <div class="col">
                <div class="mb-3">
                  <label for="end" class="form-label">Renewal period end date</label>
                  <input type="date" class="form-control" id="end" name="end" placeholder="<?= htmlspecialchars($endDate->format('Y-m-d')) ?>" value="<?= htmlspecialchars($endDate->format('Y-m-d')) ?>" required>
                </div>
              </div>
            </div>

            <p>
              The renewal period start and end dates should define the period in which people can complete membership renewal.
            </p>

            <p>
              Required stages
            </p>

            <div class="mb-3">
              <?php foreach ($stages as $stage => $details) { ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="<?= htmlspecialchars($stage . '-main-check') ?>" name="<?= htmlspecialchars($stage . '-main-check') ?>" <?php if ($details['required']) { ?>checked<?php } ?> <?php if ($details['required_locked']) { ?>disabled<?php } ?>>
                  <label class="form-check-label" for="<?= htmlspecialchars($stage . '-main-check') ?>">
                    <?= htmlspecialchars($stageNames[$stage]) ?>
                  </label>
                </div>
              <?php } ?>
            </div>

            <p>
              Member information stage includes the following;
            </p>

            <div class="mb-3">
              <?php foreach ($memberStages as $stage => $details) { ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="<?= htmlspecialchars($stage . '-member-check') ?>" name="<?= htmlspecialchars($stage . '-member-check') ?>" <?php if ($details['required']) { ?>checked<?php } ?> <?php if ($details['required_locked']) { ?>disabled<?php } ?>>
                  <label class="form-check-label" for="<?= htmlspecialchars($stage . '-member-check') ?>">
                    <?= htmlspecialchars($memberStageNames[$stage]) ?>
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
                Add renewal period
              </button>
            </p>

          <?php } else { ?>

            <div class="alert alert-warning">
              <p class="mb-0">
                <strong>Please create a membership year which ends in future before creating a renewal period</strong>
              </p>
            </div>

          <?php } ?>
        </form>
      </main>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('js/NeedsValidation.js');
$footer->render();
