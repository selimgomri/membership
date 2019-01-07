<?php

$use_white_background = true;

$id = mysqli_real_escape_string($link, $id);
$sql = "SELECT * FROM `moves` WHERE `MemberID` = '$id';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);

$name = $currentSquad = "";

if ($count > 0) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$moveID = $row['MoveID'];
	header("Location: " . autoUrl("squads/moves/edit/" . $moveID));
} else {
	$sql = "SELECT `MForename`, `MSurname`, `SquadName`, members.SquadID FROM `members` INNER JOIN `squads` ON members.SquadID = squads.SquadID WHERE `MemberID` = '$id';";
	$result = mysqli_query($link, $sql);
	$count = mysqli_num_rows($result);
	if ($count != 1) {
		halt(404);
	}
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$name = $row['MForename'] . " " . $row['MSurname'];
	$currentSquad = $row['SquadName'];
	$squadID = $row['SquadID'];

	$sql = "SELECT `SquadName`, `SquadID` FROM `squads` WHERE `SquadID` != '$squadID' ORDER BY `SquadFee` DESC, `SquadName` ASC;";
	$result = mysqli_query($link, $sql);
	$count = mysqli_num_rows($result);
	if ($count < 1) {
		halt(404);
	}
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
				    <option selected>Choose...</option>
						<?php for ($i=0; $i<$count; $i++) {
							$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
							$id = $row['SquadID'];
							$name = $row['SquadName']; ?>
							<option value="<?php echo $id ?>"><?php echo $name ?></option>
						<?php } ?>
				  </select>
		    </div>
		  </div>
			<div class="form-group row">
		    <label for="movingDate" class="col-sm-2 col-form-label">Moving Date</label>
		    <div class="col-sm-10">
		      <input type="date" class="form-control" id="movingDate" name="movingDate" value="<?php echo date("Y-m-d"); ?>">
		    </div>
		  </div>
			<button type="submit" class="btn btn-dark">Save Move</button>
		</form>
	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
