<?php

global $db;

$date = new DateTime('-9 years last day of December', new DateTimeZone('Europe/London'));
$now = new DateTime('now', new DateTimeZone('Europe/London'));

$getMembers = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, SquadName squad, DateOfBirth dob FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE DateOfBirth <= ? AND ASACategory = ? ORDER BY MForename ASC, MSurname ASC");
$getMembers->execute([
  $date->format("Y-m-d"),
  1
]);
$member = $getMembers->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Upgradeable Members";

$fluidContainer = true;

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("members"))?>">Members</a></li>
      <li class="breadcrumb-item active" aria-current="page">Upgradeable Members</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Upgradeable members</h1>
      <p class="lead">Upgradeable members are nine years old* by the end of the year and are Category 1 Swim England members.</p>
      <p>This report helps you identify which members will need to be upgraded to Category 2 membership for next year.</p>

      <?php if ($member) { ?>
        <div class="list-group mb-3">
          <?php do {
            $dob = new DateTime($member['dob'], new DateTimeZone('Europe/London'));
            $age = $dob->diff($now)->y; ?>
          <a href="<?=htmlspecialchars(autoUrl("members/" . $member['id']))?>" class="list-group-item list-group-item-action">
            <div class="row align-items-center">
              <div class="col-md">
                <strong><?=htmlspecialchars($member['fn'] . ' ' . $member['sn'])?></strong>, <?=htmlspecialchars($member['squad'])?>
              </div>
              <div class="col-md text-md-right">
                <?=htmlspecialchars($dob->format("j F Y"))?> (<?=htmlspecialchars($age)?>)
              </div>
            </div>
          </a>
          <?php } while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)); ?>
        </div>
      <?php } else { ?>
        <div class="alert alert-info">
          <p class="mb-0"><strong>There are no upgradeable members at the moment.</strong></p>
          <p>Upgradeable members are nine years old by the end of the year and are Category 1 Swim England members.</p>
        </div>
      <?php } ?>

      <p>* Born on or after <?=htmlspecialchars($date->format("j F Y"))?></p>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';