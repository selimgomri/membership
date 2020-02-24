<?php

global $db;

$moves = $db->query("SELECT
  moves.MemberID,
  MForename,
  MSurname,
  new.`SquadName` AS NewSquad,
  current.SquadName CurrentSquad,
  current.SquadID CurrentSquadID,
  moves.SquadID,
  `MovingDate`,
  `MoveID`
  FROM
  (
    (
      (`moves`
        JOIN squads AS new ON moves.SquadID = new.SquadID
      )
      JOIN `members` ON members.MemberID = moves.MemberID
    )
    JOIN `squads` AS current ON members.SquadID = current.SquadID
  )
  WHERE MovingDate >= CURDATE() ORDER BY `MForename` ASC, `MSurname` ASC
");
$move = $moves->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Squad Moves";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php";
?>
<div class="container">
  <div class="row">
    <div class="col">
    	<h1>Squad Moves</h1>
    	<p class="lead">Upcoming Squad Moves (Sorted by Date)</p>
    	<p>To make a new squad move, <a href="<?=autoUrl("members")?>">select a member</a>.</p>
    	<!-- LIST -->
    	<?php if ($move != null) { ?>
        <div class="card">
          <div class="card-header">
            All moves
          </div>
          <ul class="list-group list-group-flush">
    					<?php do { 
                $moveDate = new DateTime($move['MovingDate'], new DateTimeZone('Europe/London')); 
              ?>
    					<li class="list-group-item list-group-item-action">
                <div class="form-row align-items-center">
                  <div class="col">
                    <p class="mb-0">
                      <strong><a href="<?=autoUrl("swimmers/" .
                      $move['MemberID'])?>"><?=htmlspecialchars($move['MForename'] . " " .
                      $move['MSurname'])?></a></strong>
                    </p>
                    <p class="mb-0">
                      <span class="sr-only">Moving from</span> <a
                      href="<?=autoUrl("squads/" .
                      $move['CurrentSquadID'])?>"><?=htmlspecialchars($move['CurrentSquad'])?>
                      Squad</a> <i class="fa fa-long-arrow-right"
                      aria-hidden="true"></i><span class="sr-only">to</span> <a
                      href="<?=autoUrl("squads/" .
                      $move['SquadID'])?>"><?=htmlspecialchars($move['NewSquad'])?>
                      Squad</a> on <?=htmlspecialchars($moveDate->format("j F Y"))?>
                    </p>
                    <div class="d-lg-none mb-3"></div>
                  </div>
                  <div class="col-md-4 col-lg-3">
                    <div class="form-row">
                      <div class="col-6 col-lg-12">
            						<a class="btn btn-block btn-outline-dark" href="<?=autoUrl("swimmers/" . $move['MemberID'] . "/edit-move")?>">
            							Edit or Cancel
            						</a>
                        <div class="d-none d-lg-block mb-1"></div>
                      </div>
                      <div class="col-6 col-lg-12">
            						<a class="btn btn-block btn-outline-dark" href="<?=autoUrl("swimmers/" . $move['MemberID'] . "/move-contract")?>">
            							Print Contract
            						</a>
                      </div>
                    </div>
                  </div>
                </div>

    					</li>
            <?php } while ($move = $moves->fetch(PDO::FETCH_ASSOC)); ?>
          </ul>
        </div>
    	<?php }
    	else { ?>
    	<div class="alert alert-warning">
    		<strong>There are no upcoming squad moves</strong> <br>
    		Check back regularly to see which swimmers may be moving into your squad
    	</div>
    	<?php } ?>
    </div>
  </div>
</div>
<?php include BASE_PATH . "views/footer.php";
