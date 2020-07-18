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

$guests = $members = null;
if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn']) && bool($_SESSION['TENANT-' . app()->tenant->getId()]['LoggedIn'])) {
  $guests = $db->prepare("SELECT ID, GuestName, GuestPhone FROM covidVisitors WHERE Inputter = ?");
  $guests->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
  ]);
  $members = $db->prepare("SELECT MForename fn, MSurname sn, MemberID `id` FROM members WHERE `UserID` = ? ORDER BY fn ASC, sn ASC");
  $members->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
  ]);
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

        <form method="post" class="needs-validation" novalidate>

          <p>
            We've already got you down!
          </p>

          <p>
            If there's nobody else, just check in now
          </p>

          <?php if ($member = $members->fetch(PDO::FETCH_ASSOC)) { ?>
            <h3>Members</h3>
            <?php do { ?>
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="<?= htmlspecialchars('member-' . $member['id']) ?>" name="<?= htmlspecialchars('member-' . $member['id']) ?>">
                <label class="custom-control-label" for="<?= htmlspecialchars('member-' . $member['id']) ?>"><?= htmlspecialchars($member['fn'] . ' ' . $member['sn']) ?></label>
              </div>
            <?php } while ($member = $members->fetch(PDO::FETCH_ASSOC)); ?>
            <div class="mb-3"></div>
          <?php } ?>

          <?php if ($guest = $guests->fetch(PDO::FETCH_ASSOC)) { ?>
            <h3>Previous guests</h3>
            <?php do { ?>
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="<?= htmlspecialchars('guest-' . $guest['ID']) ?>" name="<?= htmlspecialchars('guest-' . $guest['ID']) ?>">
                <label class="custom-control-label" for="<?= htmlspecialchars('guest-' . $guest['ID']) ?>"><?= htmlspecialchars($guest['GuestName']) ?> <em><?= htmlspecialchars($guest['GuestPhone']) ?></em></label>
              </div>
            <?php } while ($guest = $guests->fetch(PDO::FETCH_ASSOC)); ?>
            <div class="mb-3"></div>
          <?php } ?>

          <h3>Guests</h3>

          <p>
            Press <strong>Add a guest</strong> to add as many other people as required.
          </p>

          <div id="guests-box" data-init="false"></div>

          <p>
            <button class="btn btn-primary" id="add-guest" type="button">
              Add a guest
            </button>
          </p>

          <hr>

          <?= SCDS\CSRF::write() ?>

          <p>
            <button type="submit" class="btn btn-success">
              Check in
            </button>
          </p>
        </form>
      <?php } else { ?>
        <h2>
          You're a guest
        </h2>

        <p>
          If you have an account, <a href="<?= htmlspecialchars(autoUrl('login?target=' . urlencode($tenant->getCodeId() . '/contact-tracing/check-in/' . $id))) ?>">please sign in</a> so we can pre-fill your details
        </p>

        <form method="post" class="needs-validation" novalidate>

          <h3>Guests</h3>

          <p>
            Please start with your own details, then add those of any others who are with you.
          </p>

          <div id="guests-box" data-init="true"></div>

          <p>
            <button class="btn btn-primary" id="add-guest" type="button">
              Add a guest
            </button>
          </p>

          <hr>

          <?= SCDS\CSRF::write() ?>

          <p>
            <button type="submit" class="btn btn-success">
              Check-in
            </button>
          </p>
        </form>
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
$footer->addJs('public/js/contact-tracing/check-in.js');
$footer->render();
