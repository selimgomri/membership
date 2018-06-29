<?php

$pagetitle = "Squad Moves";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php";
$sql = "SELECT moves.MemberID, `MForename`, `MSurname`, `SquadName`, moves.SquadID, `MovingDate`, `MoveID` FROM ((`moves` INNER JOIN `members` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON squads.SquadID = moves.SquadID) WHERE MovingDate >= CURDATE() ORDER BY `MForename` ASC, `MSurname` ASC;";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
?>
<div class="container">
	<h1>Squad Moves</h1>
	<p class="lead">Upcoming Squad Moves (Sorted by Date)</p>
	<p>To make a new squad move, <a href="<?php echo autoUrl("swimmers") ?>">select a swimmer</a>.</p>
	<!-- TABLE HERE -->
	<?php if ($count > 0) { ?>
		<div class="table-resonsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Swimmer</th>
						<th>New Squad</th>
						<th>Moves on</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i < $count; $i++) {
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
					<tr>
						<td>
							<a href="<?php echo autoUrl("swimmers/" . $row['MemberID']); ?>">
								<?php echo $row['MForename'] . " " . $row['MSurname']; ?>
							</a>
						</td>
						<td>
							<a href="<?php echo autoUrl("squads/" . $row['SquadID']); ?>">
								<?php echo $row['SquadName']; ?>
							</a>
						</td>
						<td><?php echo date('j F Y', strtotime($row['MovingDate'])); ?></td>
						<td>
							<a href="<?php echo autoUrl("squads/moves/edit/" . $row['MoveID']); ?>">
								Edit or Cancel Move
							</a>
						</td>
					</tr>
					<?php } ?>
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
