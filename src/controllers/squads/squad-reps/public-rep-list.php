<?php

$pagetitle = "Squad Reps";

global $db;

$getMyReps = $db->prepare("SELECT Forename, Surname, MForename, MSurname, SquadName FROM (((members INNER JOIN squads ON members.SquadID = squads.SquadID) INNER JOIN squadReps ON squads.SquadID = squadReps.Squad) INNER JOIN users ON squadReps.User = users.UserID) WHERE members.UserID = ?");
$getMyReps->execute([
  $_SESSION['UserID']
]);
$myReps = $getMyReps->fetch(PDO::FETCH_ASSOC);

$getAllReps = $db->query("SELECT Forename, Surname, SquadName FROM ((squadReps INNER JOIN squads ON squads.SquadID = squadReps.Squad) INNER JOIN users ON squadReps.User = users.UserID)");
$allReps = $getAllReps->fetch(PDO::FETCH_ASSOC);

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

      <ul class="list-group">
      <?php do { ?>
        <li class="list-group-item">
          <h3><?=htmlspecialchars($allReps['Forename'] . ' ' . $allReps['Surname'])?><br><small><?=htmlspecialchars($allReps['SquadName'])?> Squad</small></h3>
        </li>
      <?php } while ($allReps = $getAllReps->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
      <?php } ?>


      <h2>
        All squad reps
      </h2>
      <p class="lead">
        Reps for all squads
      </p>

      <?php if ($allReps != null) { ?>
        <ul class="list-group">
        <?php do { ?>
          <li class="list-group-item">
            <h3><?=htmlspecialchars($allReps['Forename'] . ' ' . $allReps['Surname'])?><br><small><?=htmlspecialchars($allReps['SquadName'])?> Squad</small></h3>
          </li>
        <?php } while ($allReps = $getAllReps->fetch(PDO::FETCH_ASSOC)); ?>
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