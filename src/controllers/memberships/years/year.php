<?php

$db = app()->db;
$tenant = app()->tenant;

$getYear = $db->prepare("SELECT `Name`, `StartDate`, `EndDate` FROM `membershipYear` WHERE `ID` = ? AND `Tenant` = ?");
$getYear->execute([
  $id,
  $tenant->getId(),
]);
$year = $getYear->fetch(PDO::FETCH_ASSOC);

if (!$year) halt(404);

$yearMembers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID member, Active current FROM membershipYearMembers INNER JOIN members ON membershipYearMembers.Member = members.MemberID WHERE `Year` = ? ORDER BY sn ASC, fn ASC;");
$yearMembers->execute([
  $id,
]);
$member = $yearMembers->fetch(PDO::FETCH_OBJ);

$pagetitle = htmlspecialchars($year['Name']) . " - Membership Years - Membership Centre";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships")) ?>">Memberships</a></li>
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships/years")) ?>">Years</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($year['Name']) ?></li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($year['Name']) ?>
        </h1>
        <p class="lead mb-0">
          Full membership details for <?= htmlspecialchars($year['Name']) ?>
        </p>
      </div>
      <div class="col-auto ms-lg-auto">
        <a href="<?= htmlspecialchars(autoUrl("memberships/years/$id/edit")) ?>" class="btn btn-success">Edit</a>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row">
    <div class="col-lg-8">
      <p>
        The Membership Centre lets clubs track which memberships their members hold in a given year.
      </p>

      <?php if ($member) { ?>
        <ul class="list-group mb-3">
          <?php do { ?>
            <li class="list-group-item">
              <div>
                <?php if (!$member->current) { ?><s><?php } ?><?= htmlspecialchars($member->fn . ' ' . $member->sn) ?><?php if (!$member->current) { ?></s> (Member has left club)<?php } ?>
              </div>

            </li>
          <?php } while ($member = $yearMembers->fetch(PDO::FETCH_OBJ)); ?>
        </ul>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>There are no members for this membership year</strong>
          </p>
        </div>
      <?php } ?>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
