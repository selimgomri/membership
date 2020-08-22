<?php

$db = app()->db;
$tenant = app()->tenant;
$pagetitle = 'COVID Health Screening';

$squad = null;

$user = app()->user;
if ($user->hasPermission('Admin') || $user->hasPermission('Coach') || $user->hasPermission('Galas')) {
  $showSquadOpts = true;
  $getSquad = $db->prepare("SELECT SquadName, SquadID FROM squads WHERE squads.Tenant = ? AND squads.SquadID = ?;");
  $getSquad->execute([
    $tenant->getId(),
    $id,
  ]);
  $squad = $getSquad->fetch(PDO::FETCH_ASSOC);
} else {
  $getSquad = $db->prepare("SELECT SquadName, SquadID FROM squads INNER JOIN squadReps ON squads.SquadID = squadReps.Squad WHERE squadReps.User = ? AND squadReps.Squad = ?;");
  $getSquad->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
    $id,
  ]);
  $squad = $getSquad->fetch(PDO::FETCH_ASSOC);
}

if (!$squad) {
  halt(404);
}

$getMembers = $db->prepare("SELECT MForename, MSurname, MemberID FROM members INNER JOIN squadMembers ON squadMembers.Member = members.MemberID WHERE squadMembers.Squad = ? ORDER BY MForename ASC, MSurname ASC;");
$getMembers->execute([
  $id,
]);
$member = $getMembers->fetch(PDO::FETCH_ASSOC);

$getLatestCompletion = $db->prepare("SELECT `ID`, `DateTime`, `OfficerApproval` FROM covidHealthScreen WHERE Member = ? ORDER BY `DateTime` DESC LIMIT 1");

$pagetitle = htmlspecialchars($squad['SquadName']) . ' - Health Screening';

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('covid')) ?>">COVID</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('covid/health-screening')) ?>">Screening</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('covid/health-screening#squads')) ?>">Squads</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($squad['SquadName']) ?></li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($squad['SquadName']) ?> Health Screening
        </h1>
        <p class="lead mb-0">
          Keeping everyone safe
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row">

    <div class="col-lg-8">

      <?php if ($member) { ?>

        <ul class="list-group mb-3">
          <?php do {
            $getLatestCompletion->execute([
              $member['MemberID']
            ]);

            $latest = $getLatestCompletion->fetch(PDO::FETCH_ASSOC);
          ?>
            <li class="list-group-item">
              <div class="row align-items-center">
                <div class="col-sm">
                  <h3 class="h6 mb-1"><?= htmlspecialchars($member['MForename'] . ' ' . $member['MSurname']) ?><?php if ($latest && bool($latest['OfficerApproval'])) { ?> <span class="badge badge-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Approved by club</span><?php } ?></h3>
                  <p class="mb-0">
                    <?php if ($latest) {
                      $time = new DateTime($latest['DateTime'], new DateTimeZone('UTC'));
                      $time->setTimezone(new DateTimeZone('Europe/London'));
                    ?>
                      Latest submission <?= htmlspecialchars($time->format('H:i, j F Y')) ?>
                    <?php } else { ?>
                      No survey submitted
                    <?php } ?>
                  </p>
                </div>
                <div class="col-auto">
                  <div class="btn-group">
                    <?php if ($latest) { ?>
                      <a href="<?= htmlspecialchars(autoUrl('covid/health-screening/members/' . $member['MemberID'])) ?>" class="btn btn-dark">View all</a>
                    <?php } ?>
                    <a href="<?= htmlspecialchars(autoUrl('covid/health-screening/members/' . $member['MemberID'] . '/new-survey')) ?>" class="btn btn-success">New submission</a>
                  </div>
                </div>
              </div>
            </li>
          <?php } while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>There are no members in <?= htmlspecialchars($squad['SquadName']) ?></strong>
          </p>
        </div>
      <?php } ?>

      <p>
        Information about COVID-19 is available <a href="https://www.nhs.uk/conditions/coronavirus-covid-19/" target="_blank">on the NHS website</a>.
      </p>
    </div>

  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
