<?php

global $db;

$getMove = $db->prepare("SELECT members.MemberID, `MForename`, `MSurname`, `SquadName`, moves.SquadID, `MovingDate` FROM ((`moves` INNER JOIN `members` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON squads.SquadID = moves.SquadID) WHERE moves.MemberID = ?");
$getMove->execute([$id]);
$move = $getMove->fetch(PDO::FETCH_ASSOC);

$date = new DateTime('now', new DateTimeZone('Europe/London'));

if ($move == null) {
	halt(404);
}

$memberID = $move['MemberID'];
$name = $move['MForename'] . " " . $move['MSurname'];
$squadID = $move['SquadID'];
$movingDate = $move['MovingDate'];

$getSquad = $db->prepare("SELECT `SquadName`, squads.SquadID FROM `squads` INNER JOIN `members` ON squads.SquadID = members.SquadID WHERE `MemberID` = ?");
$getSquad->execute([$id]);
$current = $getSquad->fetch(PDO::FETCH_ASSOC);

if ($current == null) {
  halt(404);
}

$currentSquad = $current['SquadName'];
$currentSquadID = $current['SquadID'];

$getSquads = $db->prepare("SELECT `SquadName`, `SquadID` FROM `squads` WHERE `SquadID` != ? ORDER BY `SquadFee` DESC, `SquadName` ASC");
$getSquads->execute([$currentSquadID]);

$pagetitle = "Squad Move for " . htmlspecialchars($name);
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php"; ?>
<div class="container">
	<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("squads/moves")?>">Squad Moves</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit - <?=htmlspecialchars($move['MForename'] . " " . $move['MSurname'][0])?></li>
    </ol>
  </nav>
	<div class="">
		<h1>Squad Move for <?=htmlspecialchars($name)?></h1>
		<?php if (isset($_SESSION['ErrorState'])) {
			echo $_SESSION['ErrorState'];
			unset($_SESSION['ErrorState']);
		} ?>
		<form method="post">
			<div class="form-group row">
		    <label for="swimmerName" class="col-sm-2 col-form-label">Swimmer</label>
		    <div class="col-sm-10">
		      <input type="text" readonly class="form-control" id="swimmerName" name="swimmerName" value="<?=htmlspecialchars($name)?>" disabled>
		    </div>
		  </div>
			<div class="form-group row">
		    <label for="currentSquad" class="col-sm-2 col-form-label">Current Squad</label>
		    <div class="col-sm-10">
		      <input type="text" readonly class="form-control" id="currentSquad" name="currentSquad" value="<?=htmlspecialchars($currentSquad)?>" disabled>
		    </div>
		  </div>
		  <div class="form-group row">
		    <label for="newSquad" class="col-sm-2 col-form-label">New Squad</label>
		    <div class="col-sm-10">
					<select class="custom-select" id="newSquad" name="newSquad">
						<!-- HIDES CURRENT SQUAD AS POINTLESS -->
						<?php while ($row = $getSquads->fetch(PDO::FETCH_ASSOC)) { ?>
							<option value="<?=$row['SquadID']?>" <?php if ($row['SquadID'] == $move['SquadID']) { echo "selected";} ?> ><?=htmlspecialchars($row['SquadName'])?></option>
						<?php } ?>
				  </select>
		    </div>
		  </div>
			<div class="form-group row">
		    <label for="movingDate" class="col-sm-2 col-form-label">Moving Date</label>
		    <div class="col-sm-10">
		      <input type="date" class="form-control" id="movingDate" name="movingDate" min="<?=htmlspecialchars($date->format("Y-m-d"))?>" value="<?=htmlspecialchars($movingDate)?>">
		    </div>
		  </div>
			<button type="submit" class="btn btn-dark">Save Move</button> <a class="btn btn-danger" href="<?=autoUrl("swimmers/" . $id . "/cancel-move")?>">Cancel Move</a>
		</form>
	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
