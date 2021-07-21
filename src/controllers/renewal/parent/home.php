<?php

$db = app()->db;
$user = app()->user;
$tenant = app()->tenant;

$date = new DateTime('now', new DateTimeZone('Europe/London'));
$renewals = $db->prepare("SELECT * FROM `renewals` WHERE `StartDate` <= :today AND `EndDate` >= :today AND Tenant = :tenant");
$renewals->execute([
  'tenant' => $tenant->getId(),
  'today' => $date->format("Y-m-d")
]);
$row = $renewals->fetch(PDO::FETCH_ASSOC);

// Validate ready
$getNum = $db->prepare("SELECT COUNT(*) FROM renewalMembers WHERE RenewalID = ?");
$getNum->execute([
  $row['ID'],
]);

$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container-xl">
  <div class="row">
    <div class="col-lg-8">
      <?php if ($row != null) {

        $end = new DateTime($row['EndDate'], new DateTimeZone('Europe/London'));

      ?>
        <h1>
          Membership Renewal
        </h1>
        <p class="lead">
          Welcome to the Membership Renewal System
        </p>
        <?php if (app()->tenant->getBooleanKey('REQUIRE_FULL_RENEWAL')) { ?>
          <p>
            Membership renewal ensures all our information about you is up to date, that you and your swimmers understand your rights and responsibilities at the club, and that you can pay your <abbr title="Including Swim England Membership Fees"> membership fee</abbr> for the year ahead.
          </p>
          <p>
            The Membership Renewal Period is open until <?= htmlspecialchars($end->format('l j F Y')) ?>.
          </p>
          <p>
            Do not worry if you make a mistake while filling out any forms. You can edit all information at any time.
          </p>
          <p>
            We'll save your progress as you fill out the required forms.
          </p>
        <?php } else { ?>

          <p>
            We'll jump straight to the membership fees page.
          </p>

        <?php } ?>
        <?php if ($getNum->fetchColumn() == 0) { ?>
          <div class="alert alert-info">
            <p class="mb-0">
              <strong>This membership renewal period has opened, but is not yet ready</strong>
            </p>
            <p class="mb-0">
              Please check back later.
            </p>

            <?php if ($user->hasPermission('Admin')) { ?>
              <div class="mt-3"></div>
              <hr>
              <p class="mb-0">
                <strong>Administrator message</strong>
              </p>
              <p>
                Hi <?= htmlspecialchars($user->getFirstName()) ?>. This membership renewal period has opened, but the membership renewal list has not yet been generated. This may be due to a fault, or you may need to await the execution of an overnight batch process.
              </p>
              <p class="mb-0">
                If this issue still persists tomorrow morning, please contact SCDS support at <a href="mailto:support@myswimmingclub.uk" class="alert-link">support@myswimmingclub.uk</a>.
              </p>
            <?php } ?>
          </div>
        <?php } else { ?>
          <p>
            <a class="btn btn-success" href="<?= htmlspecialchars(autoUrl("renewal/go")) ?>">
              Get Started
            </a>
          </p>
        <?php } ?>
      <?php } else { ?>
        <h1>
          Membership Renewal
        </h1>
        <p class="lead">
          Welcome to the Membership Renewal System
        </p>
        <p>
          Membership renewal ensures all our information about you is up to date, that you and your swimmers understand your rights and responsibilities at the club, and that you can pay your <abbr title="Including Swim England Membership Fees"> membership fee</abbr> for the year ahead.
        </p>
        <div class="alert alert-danger">
          <p class="mb-0">
            <strong>The membership renewal period for the next year has not yet started</strong>
          </p>
          <p class="mb-0">
            We'll let you know when this starts
          </p>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
