<?php

use function GuzzleHttp\json_decode;

$db = app()->db;
$tenant = app()->tenant;

$getLocation = $db->prepare("SELECT `ID`, `Name`, `Address` FROM covidLocations WHERE `ID` = ? AND `Tenant` = ?");
$getLocation->execute([
  $id,
  $tenant->getId()
]);
$location = $getLocation->fetch(PDO::FETCH_ASSOC);

if (!$location) {
  halt(404);
}

$pagetitle = 'Check In to ' . htmlspecialchars($location['Name']) . ' - Contact Tracing';

$addr = json_decode($location['Address']);

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('contact-tracing')) ?>">Tracing</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('contact-tracing/locations')) ?>">Locations</a></li>
        <li class="breadcrumb-item active" aria-current="page">Check In</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Check in to <?= htmlspecialchars($location['Name']) ?>
        </h1>
        <p class="lead mb-0">
          <?= htmlspecialchars($addr->streetAndNumber) ?>
        </p>
        <div class="mb-3 d-lg-none"></div>
      </div>
    </div>

  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8">
      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn']) && bool($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn'])) { ?>
        <h2>
          Tell us who's with you
        </h2>
      <?php } else { ?>
        <h2>
          You're a guest
        </h2>

        <p>
          If you have an account, <a href="<?= htmlspecialchars(autoUrl('login?target=' . urlencode($tenant->getCodeId() . '/contact-tracing/check-in/' . $id))) ?>">please sign in</a> so we can pre-fill your details
        </p>
      <?php } ?>
    </div>
    <div class="col">
      <div class="cell">
        <h2>
          What do I need to do?
        </h2>
        <p class="lead">
          Every time you visit a session run by <?= htmlspecialchars($tenant->getName()) ?>, you should check in to your current location.
        </p>

        <p>
          Only one member of a party needs to check in. If you're a member and have an account with us, you can tick to say which of your members are with you today.
        </p>

        <p>
          All users can also register the details of guests (who are not members of the club) who are attending with you.
        </p>
      </div>

    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
