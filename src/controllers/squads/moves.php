<?php

global $db;

$moves = $db->query("SELECT moves.MemberID, `MForename`, `MSurname`, `SquadName`, moves.SquadID, `MovingDate`, `MoveID` FROM ((`moves` INNER JOIN `members` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON squads.SquadID = moves.SquadID) WHERE MovingDate >= CURDATE() ORDER BY `MForename` ASC, `MSurname` ASC");
$move = $moves->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Squad Moves";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php";
?>
<div class="container">
	<h1>Squad Moves</h1>
	<p class="lead">Upcoming Squad Moves (Sorted by Date)</p>
	<p>To make a new squad move, <a href="<?=autoUrl("swimmers")?>">select a swimmer</a>.</p>
	<!-- TABLE HERE -->
	<?php if ($move != null) { ?>
		<div class="table-resonsive">
			<table class="table table-striped">
				<thead class="thead-light">
					<tr>
						<th>Swimmer</th>
						<th>New Squad</th>
						<th>Moves on</th>
						<th></th>
            <th></th>
					</tr>
				</thead>
				<tbody>
					<?php do { ?>
					<tr>
						<td>
							<a href="<?=autoUrl("swimmers/" . $move['MemberID'])?>">
								<?=htmlspecialchars($move['MForename'] . " " . $move['MSurname'])?>
							</a>
						</td>
						<td>
							<a href="<?=autoUrl("squads/" . $move['SquadID'])?>">
								<?=htmlspecialchars($move['SquadName'])?>
							</a>
						</td>
						<td><?=date('j F Y', strtotime($move['MovingDate']))?></td>
						<td>
							<a href="<?=autoUrl("swimmers/" . $move['MemberID'] . "/edit-move")?>">
								Edit or Cancel Move
							</a>
						</td>
            <td>
							<a href="<?=autoUrl("swimmers/" . $move['MemberID'] . "/move-contract")?>">
								Print Contract
							</a>
						</td>
					</tr>
        <?php } while ($move = $moves->fetch(PDO::FETCH_ASSOC)); ?>
				</tbody>
			</table>
		</div>
	<?php }
	else { ?>
	<div class="alert alert-warning">
		<strong>There are no upcoming squad moves</strong> <br>
		Check back regularly to see which swimmers may be moving into your squad
	</div>
	<?php } ?>
</div>
<?php include BASE_PATH . "views/footer.php";
