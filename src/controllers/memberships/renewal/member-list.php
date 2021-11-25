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

$stages = SCDS\Onboarding\Session::getDefaultRenewalStages();
$stageNames = SCDS\Onboarding\Session::stagesOrder();
$memberStages = SCDS\Onboarding\Member::getDefaultStages();
$memberStageNames = SCDS\Onboarding\Member::stagesOrder();

$perPage = 15;

$pagination = new \SCDS\Pagination();
$pagination->records_per_page($perPage);

$basePageTitle = htmlspecialchars($renewal->start->format('j M Y') . " - " . $renewal->end->format('j M Y')) . " - Renewal - Membership Centre";
$pagetitle = "Member List - " . $basePageTitle;

$getCount = $getMembers = null;
// If else
if ($type == "member-list") {
  $getCount = $db->prepare("SELECT COUNT(*) FROM onboardingMembers INNER JOIN members ON onboardingMembers.member = members.MemberID INNER JOIN onboardingSessions ON onboardingMembers.session = onboardingSessions.id WHERE onboardingSessions.renewal = ?");
  $getCount->execute([
    $id
  ]);
  $getMembers = $db->prepare("SELECT members.MForename `firstName`, members.MSurname `lastName` FROM onboardingMembers INNER JOIN members ON onboardingMembers.member = members.MemberID INNER JOIN onboardingSessions ON onboardingMembers.session = onboardingSessions.id WHERE onboardingSessions.renewal = :sessionId ORDER BY MSurname ASC, MForename ASC LIMIT :offset, :num");
  $getMembers->bindValue(':sessionId', $id, PDO::PARAM_INT);
  $getMembers->bindValue(':offset', $pagination->get_limit_start(), PDO::PARAM_INT);
  $getMembers->bindValue(':num', $perPage, PDO::PARAM_INT);
  $getMembers->execute();
  $pagetitle = "Members in this renewal - " . $basePageTitle;
} else if ($type = "current-members-not-in-renewal-list") {
  $pagetitle = "Current members not in this renewal - " . $basePageTitle;
} else {
  halt(404);
}

$count = $getCount->fetchColumn();
$member = $getMembers->fetch(PDO::FETCH_OBJ);

if ($pagination->get_limit_start() > 1 && $pagination->get_limit_start() >= $count) halt(404);

$pagination->records($count);

include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships")) ?>">Memberships</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships/renewal")) ?>">Renewal</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships/renewal/$id")) ?>"><?= htmlspecialchars($renewal->start->format('d/m/Y')) ?> - <?= htmlspecialchars($renewal->end->format('d/m/Y')) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Member list</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col">
        <h1>
          Member list for <?= htmlspecialchars($renewal->start->format('j M Y')) ?> - <?= htmlspecialchars($renewal->end->format('j M Y')) ?> Renewal
        </h1>
        <p class="lead mb-0">
          For the <?php if ($renewal->clubYear) { ?> club membership year <?= htmlspecialchars($renewal->clubYear->start->format('j M Y')) ?> - <?= htmlspecialchars($renewal->clubYear->end->format('j M Y')) ?><?php } ?><?php if ($renewal->clubYear && $renewal->ngbYear) { ?> and the <?php } ?><?php if ($renewal->ngbYear) { ?> Swim England membership year <?= htmlspecialchars($renewal->ngbYear->start->format('j M Y')) ?> - <?= htmlspecialchars($renewal->ngbYear->end->format('j M Y')) ?><?php } ?>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row">
    <div class="col-lg-8">
      <main>

        <?php if ($member) { ?>

          <p>
            <?= htmlspecialchars($count) ?> records
          </p>

          <?php if ($type == "member-list") { ?>
            <p>
              A renewal session was created for members on this list. Some members may however have chosen not to renew.
            </p>
          <?php } ?>

          <div class="list-group mb-3">
            <?php do { ?>
              <a href="<?= htmlspecialchars(autoUrl("members/")) ?>" class="list-group-item list-group-item-action">
                <h2><?= htmlspecialchars(\SCDS\Formatting\Names::format($member->firstName, $member->lastNamee)) ?></h2>
              </a>
            <?php } while ($member = $getMembers->fetch(PDO::FETCH_OBJ)); ?>
          </div>

          <?= $pagination->render(); ?>
        <?php } else { ?>
          <div class="alert alert-warning">
            No records to display
          </div>
        <?php } ?>


      </main>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('js/NeedsValidation.js');
$footer->render();
