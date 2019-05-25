<?php

global $db;
$getSquad = $db->prepare("SELECT SquadName, SquadFee, SquadCoC, SquadTimetable FROM squads WHERE SquadID = ?");
$getSquad->execute([$id]);
$squad = $getSquad->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($squad['SquadName']) . ' Squad';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1><?=htmlspecialchars($squad['SquadName'])?> Squad</h1>
      <p class="lead">
        This squad has x swimmers.
      </p>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';
