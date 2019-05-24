<?php

global $db;

$moves = $db->query("SELECT moves.MemberID, `MForename`, `MSurname`, `SquadName`, moves.SquadID, `MovingDate`, `MoveID` FROM ((`moves` INNER JOIN `members` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON squads.SquadID = moves.SquadID) WHERE MovingDate >= CURDATE() ORDER BY `MForename` ASC, `MSurname` ASC");
$move = $moves->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Squad Moves";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php";
?>
<div class="container">
  <div class="row">
    <div class="col-lg-8">
    	<h1>Squad Moves</h1>
    	<p class="lead">Upcoming Squad Moves (Sorted by Date)</p>
    	<p>To make a new squad move, <a href="<?=autoUrl("swimmers")?>">select a swimmer</a>.</p>
    	<!-- TABLE HERE -->
    	<?php if ($move != null) { ?>
        <div class="card">
          <div class="card-header">
            Upcoming moves
          </div>
          <ul class="list-group list-group-flush">
    					<?php do { ?>
    					<li class="list-group-item">
                <div class="form-row align-items-center">
                  <div class="col">
                    <p class="mb-0">
                      <a href="<?=autoUrl("swimmers/" .
                      $move['MemberID'])?>"><?=htmlspecialchars($move['MForename'] . " " .
                      $move['MSurname'])?></a> moving to  <a
                      href="<?=autoUrl("squads/" .
                      $move['SquadID'])?>"><?=htmlspecialchars($move['SquadName'])?> Squad</a>
                      on <?=date('j F Y', strtotime($move['MovingDate']))?>
                    </p>
                    <div class="d-lg-none mb-3"></div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-row">
                      <div class="col-6 col-lg-12">
            						<a class="btn btn-block btn-light" href="<?=autoUrl("swimmers/" . $move['MemberID'] . "/edit-move")?>">
            							Edit or Cancel
            						</a>
                      </div>
                      <div class="col-6 col-lg-12">
            						<a class="btn btn-block btn-light" href="<?=autoUrl("swimmers/" . $move['MemberID'] . "/move-contract")?>">
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
