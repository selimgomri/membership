<?php

$db = app()->db;
$tenant = app()->tenant;

$date = new DateTime('now', new DateTimeZone('Europe/London'));
$renewals = $db->prepare("SELECT * FROM `renewals` WHERE `StartDate` <= :today AND `EndDate` >= :today AND Tenant = :tenant");
$renewals->execute([
  'tenant' => $tenant->getId(),
  'today' => $date->format("Y-m-d")
]);
$row = $renewals->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
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
    <p>
      <a class="btn btn-success" href="<?= htmlspecialchars(autoUrl("renewal/go")) ?>">
        Get Started
      </a>
    </p>
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

<?php $footer = new \SCDS\Footer();
$footer->render();
