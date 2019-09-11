<?php

$pagetitle = "Squad Reps";

global $db;

$getMyReps = $db->prepare("SELECT MForename, MSurname, Forename, Surname, MForename, MSurname, SquadName FROM (((members INNER JOIN squads ON members.SquadID = squads.SquadID) INNER JOIN squadReps ON squads.SquadID = squadReps.Squad) INNER JOIN users ON squadReps.User = users.UserID) WHERE members.UserID = ?");
$getMyReps->execute([
  $_SESSION['UserID']
]);
$myReps = $getMyReps->fetch(PDO::FETCH_ASSOC);

$getAllReps = $db->query("SELECT SquadID, Forename, Surname, SquadName FROM ((squadReps INNER JOIN squads ON squads.SquadID = squadReps.Squad) INNER JOIN users ON squadReps.User = users.UserID) ORDER BY SquadFee DESC, SquadName ASC");
$allReps = $getAllReps->fetchAll(PDO::FETCH_GROUP);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Squad Reps</h1>
      <p class="lead">
        Squad reps are here to help
      </p>

      <?php if ($myReps != null) { ?>
      <h2>
        Your squad reps
      </h2>
      <p class="lead">
        Reps for each of your swimmers
      </p>

      <ul class="list-group mb-3">
      <?php do { ?>
        <li class="list-group-item">
          <h3><?=htmlspecialchars($myReps['MForename'] . ' ' . $myReps['MSurname'])?> <small>(<?=htmlspecialchars($myReps['SquadName'])?> Squad)</small></h3>
          <p class="lead mb-0">
            <?=htmlspecialchars($myReps['Forename'] . ' ' . $myReps['Surname'])?> is your rep
          </p>
        </li>
      <?php } while ($myReps = $getMyReps->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
      <?php } ?>


      <h2>
        All reps
      </h2>
      <p class="lead">
        Here is a full list of squad reps
      </p>

      <?php if (sizeof($allReps) > 0) { ?>
        <ul class="list-group mb-3">
        <?php foreach ($allReps as $squad) { ?>
          <li class="list-group-item">
            <h3><?=htmlspecialchars($squad[0]['SquadName'])?> Squad Rep<?php if (sizeof($squad) > 1) { ?>s<?php } ?></h3>
            <ul class="list-unstyled">
              <?php foreach ($squad as $reps) { ?>
              <li><?=htmlspecialchars($reps['Forename'] . ' ' . $reps['Surname'])?></li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>
        </ul>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>
              There are no squad reps to show
            </strong>
          </p>
          <p class="mb-0">
            Your club either does not have squad reps or has not added them to the system
          </p>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';