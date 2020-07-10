<?php

// Verify user has access
canView('TeamManager', $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], $id);

$db = app()->db;
$tenant = app()->tenant;

// GET THE GALA
$getGala = $db->prepare("SELECT GalaName `name`, GalaVenue venue FROM galas WHERE GalaID = ? AND Tenant = ?");
$getGala->execute([
  $id,
  $tenant->getId()
]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
  halt(404);
}

// GET SWIMMER INFO
$getSwimmers = $db->prepare("SELECT members.MemberID id, MForename fn, MSurname sn, Website, Social, Noticeboard, FilmTraining, ProPhoto, Conditions, Allergies, Medication, Mobile, Forename pfn, Surname psn, users.UserID FROM ((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) LEFT JOIN memberMedical ON galaEntries.MemberID = memberMedical.MemberID) LEFT JOIN memberPhotography ON galaEntries.MemberID = memberPhotography.MemberID) LEFT JOIN users ON members.UserID = users.UserID) WHERE galaEntries.GalaID = ?");
$getSwimmers->execute([$id]);
$swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC);

$squads = $db->prepare("SELECT SquadName FROM squads INNER JOIN squadMembers ON squads.SquadID = squadMembers.Squad WHERE squadMembers.Member = ?");

$markdown = new ParsedownExtra();

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$mobile = null;
try {
  $mobile = PhoneNumber::parse(strval($swimmer['Mobile']));
} catch (PhoneNumberParseException | Exception $e) {
  // Do nothing we'll test for null later
}

$pagetitle = htmlspecialchars($gala['name']) . " Swimmer Information";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= autoUrl("galas") ?>">Galas</a></li>
      <li class="breadcrumb-item"><a href="<?= autoUrl("galas/" . $id) ?>">This Gala</a></li>
      <li class="breadcrumb-item"><a href="<?= autoUrl("galas/" . $id . "/team-manager") ?>">TM Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Swimmers</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">

      <h1><?= htmlspecialchars($gala['name']) ?> swimmer information</h1>
      <p class="lead">View swimmer medical information and emergency contact details</p>

      <div class="alert alert-danger">
        <p class="mb-0">You <strong>must not disclose data about any swimmers on this page</strong> to other people. If somebody needs access to this data, they will have been granted access to it via their own account.</p>
      </div>

      <?php if ($swimmer == null) { ?>

      <?php } else { ?>

        <ul class="list-group">
          <?php $i = 0;
          do {
            $squads->execute([
              $swimmer['id']
            ]);
            $s = $squads->fetchAll(PDO::FETCH_COLUMN);
          ?>
            <li class="list-group-item <?php if ($i % 2 == 1) { ?>bg-light<?php } ?>">
              <div class="row align-items-center">
                <div class="col-md">
                  <h2><?= htmlspecialchars($swimmer['fn'] . " " . $swimmer['sn']) ?></h2>
                  <p class="lead mb-0">
                    <?php for ($i = 0; $i < sizeof($s); $i++) {
                      if ($i > 0) {
                    ?>, <?php
                      }
                        ?><?= htmlspecialchars($s[$i]) ?><?php
                                                } ?>
                  </p>
                  <div class="d-md-none mb-3"></div>
                </div>
                <div class="col text-md-right">
                  <a class="btn btn-primary" data-toggle="collapse" href="#swimmer-info-box-<?= $i ?>" role="button" aria-expanded="false" aria-controls="swimmer-info-box-<?= $i ?>">
                    Show information <i class="fa fa-chevron-down" aria-hidden="true"></i>
                  </a>
                </div>
              </div>

              <div class="collapse" id="swimmer-info-box-<?= $i ?>">

                <div class="d-md-block mb-3"></div>

                <hr>

                <div class="row">
                  <div class="col-md">
                    <h3>Medical information</h3>

                    <h4>Medical conditions</h4>
                    <?php if ($swimmer['Conditions']) { ?>
                      <?= $markdown->text($swimmer['Conditions']) ?>
                    <?php } else { ?>
                      <p>None</p>
                    <?php } ?>

                    <h4>Allergies</h4>
                    <?php if ($swimmer['Allergies']) { ?>
                      <?= $markdown->text($swimmer['Allergies']) ?>
                    <?php } else { ?>
                      <p>None</p>
                    <?php } ?>

                    <h4>Medication</h4>
                    <?php if ($swimmer['Medication']) { ?>
                      <?= $markdown->text($swimmer['Medication']) ?>
                    <?php } else { ?>
                      <p>None</p>
                    <?php } ?>
                  </div>
                  <div class="col-md">
                    <?php
                    $contacts = new EmergencyContacts($db);
                    $contacts->byParent($swimmer['UserID']);
                    $contactsArray = $contacts->getContacts();
                    ?>
                    <h3>Emergency contacts</h3>
                    <?php if (sizeof($contactsArray) > 0 || $mobile != null) { ?>
                      <ul class="list-group">
                        <li class="list-group-item">
                          <p class="mb-0">
                            <strong><?= htmlspecialchars($swimmer['pfn'] . " " . $swimmer['psn']) ?></strong>
                          </p>
                          <p class="mb-0">
                            <a href="<?= htmlspecialchars($mobile->format(PhoneNumberFormat::RFC3966)) ?>">
                              <?= htmlspecialchars($mobile->format(PhoneNumberFormat::NATIONAL)) ?>
                            </a>
                          </p>
                        </li>
                        <?php for ($i = 0; $i < sizeof($contactsArray); $i++) { ?>
                          <li class="list-group-item">
                            <p class="mb-0">
                              <strong><?= htmlspecialchars($contactsArray[$i]->getName()) ?></strong>
                            </p>
                            <p class="mb-0">
                              <a href="<?= htmlspecialchars($contactsArray[$i]->getRFCContactNumber()) ?>">
                                <?= htmlspecialchars($contactsArray[$i]->getNationalContactNumber()) ?>
                              </a>
                            </p>
                          </li>
                        <?php } ?>
                      </ul>
                    <?php } ?>
                  </div>
                </div>
              </div>

            </li>
          <?php $i++;
          } while ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>

      <?php } ?>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
