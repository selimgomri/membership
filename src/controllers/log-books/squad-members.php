<?php

$db = app()->db;
$tenant = app()->tenant;

$getSquadInfo = $db->prepare("SELECT SquadName FROM squads WHERE SquadID = ? AND Tenant = ?");
$getSquadInfo->execute([
  $squad,
  $tenant->getId()
]);
$squadInfo = $getSquadInfo->fetch(PDO::FETCH_ASSOC);

if (!$squadInfo) {
  halt(404);
}

$getMembers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM members WHERE members.SquadID = ? ORDER BY fn ASC, sn ASC");
$getMembers->execute([$squad]);
$member = $getMembers->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($squadInfo['SquadName'] . " member log books");

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <h1><?=htmlspecialchars($squadInfo['SquadName'])?> log books</h1>
  <p class="lead">
    Members can log training sessions and other activity.
  </p>

  <div class="row">
    <div class="col-md-8">
      <?php if ($member) { ?>
      <div class="list-group mb-3">
        <?php do { ?>
        <a href="<?=htmlspecialchars(autoUrl("log-books/members/" . $member['id']))?>" class="list-group-item list-group-item-action">
          <p class="mb-0">
            <strong><?=htmlspecialchars($member['fn'] . ' ' . $member['sn'])?>'s log book</strong>
          </p>
        </a>
        <?php } while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)); ?>
      </div>
      <?php } else { ?>
      <?php } ?>
    </div>
    <div class="col">
      <div class="position-sticky top-3">
        <div class="cell">
          <h2>Log books are new!</h2>
          <p class="lead">
            We have added log books to the membership system as a response to the coronavirus (COVID-19) outbreak.
          </p>
          <p>
            This is to allow members to log their home based land training.
          </p>
          <p class="mb-0">
            As always, feedback is very welcome. Send it to <a href="mailto:feedback@myswimmingclub.uk">feedback@myswimmingclub.uk</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();