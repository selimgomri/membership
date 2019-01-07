<?php

$use_white_background = true;

$id = mysqli_real_escape_string($link, $id);
$sql = "SELECT members.MemberID, `MForename`, `MSurname`, `SquadName`, moves.SquadID, `MovingDate` FROM ((`moves` INNER JOIN `members` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON squads.SquadID = moves.SquadID) WHERE `MoveID` = '$id';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);

if ($count != 1) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$memberID = $row['MemberID'];
$name = $row['MForename'] . " " . $row['MSurname'];
$squadID = $row['SquadID'];
$movingDate = $row['MovingDate'];

$sql = "SELECT `SquadName`, squads.SquadID FROM `squads` INNER JOIN `members` ON squads.SquadID = members.SquadID WHERE `MemberID` = '$memberID';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
if ($count != 1) {
	//halt(404);
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$currentSquad = $row['SquadName'];
$currentSquadID = $row['SquadID'];

$sql = "SELECT `SquadName`, `SquadID` FROM `squads` WHERE `SquadID` != '$currentSquadID' ORDER BY `SquadFee` DESC, `SquadName` ASC;";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
if ($count < 1) {
	halt(404);
}

$pagetitle = "Squad Move for " . $name;
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/squadMenu.php"; ?>
<div class="container">
	<div class="">
		<h1 class="border-bottom border-gray pb-2 mb-3">Squad Move for <?php echo $name; ?></h1>
		<? if (isset($_SESSION['ErrorState'])) {
			echo $_SESSION['ErrorState'];
			unset($_SESSION['ErrorState']);
		} ?>
		<form method="post">
			<div class="form-group row">
		    <label for="swimmerName" class="col-sm-2 col-form-label">Swimmer</label>
		    <div class="col-sm-10">
		      <input type="text" readonly class="form-control" id="swimmerName" name="swimmerName" value="<?php echo $name; ?>" disabled>
		    </div>
		  </div>
			<div class="form-group row">
		    <label for="currentSquad" class="col-sm-2 col-form-label">Current Squad</label>
		    <div class="col-sm-10">
		      <input type="text" readonly class="form-control" id="currentSquad" name="currentSquad" value="<?php echo $currentSquad; ?>" disabled>
		    </div>
		  </div>
		  <div class="form-group row">
		    <label for="newSquad" class="col-sm-2 col-form-label">New Squad</label>
		    <div class="col-sm-10">
					<select class="custom-select" id="newSquad" name="newSquad">
						<!-- HIDE CURRENT SQUAD AS POINTLESS -->
						<?php for ($i=0; $i<$count; $i++) {
							$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
							$sid = $row['SquadID'];
							$name = $row['SquadName']; ?>
							<option value="<?php echo $sid ?>" <?php if ($sid == $squadID) { echo "selected";} ?>><?php echo $name ?></option>
						<?php } ?>
				  </select>
		    </div>
		  </div>
			<div class="form-group row">
		    <label for="movingDate" class="col-sm-2 col-form-label">Moving Date</label>
		    <div class="col-sm-10">
		      <input type="date" class="form-control" id="movingDate" name="movingDate" value="<?php echo $movingDate; ?>">
		    </div>
		  </div>
			<button type="submit" class="btn btn-dark">Save Move</button> <a class="btn btn-danger" href="<?php echo autoUrl("squads/moves/cancel/" . $id); ?>">Cancel Move</a>
		</form>
	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
