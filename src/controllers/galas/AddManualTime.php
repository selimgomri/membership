<?php

global $db;

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50&nbsp;Free','100&nbsp;Free','200&nbsp;Free','400&nbsp;Free','800&nbsp;Free','1500&nbsp;Free','50&nbsp;Back','100&nbsp;Back','200&nbsp;Back','50&nbsp;Breast','100&nbsp;Breast','200&nbsp;Breast','50&nbsp;Fly','100&nbsp;Fly','200&nbsp;Fly','100&nbsp;IM','150&nbsp;IM','200&nbsp;IM','400&nbsp;IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','150IMTime','200IMTime','400IMTime',];

$sql = $db->prepare("SELECT * FROM ((`galaEntries` INNER JOIN `members` ON
`members`.`MemberID` = `galaEntries`.`MemberID`) INNER JOIN `galas` ON
galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ?;");
$sql->execute([$id]);

$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
	halt(404);
}

$member = $row['MemberID'];

$type = null;
if ($row['CourseLength'] == "SHORT") {
	$type = "SCPB";
} else {
	$type = "LCPB";
}
$getTimes = $db->prepare("SELECT * FROM `times` WHERE `MemberID` = ? AND `Type` = ?;");
$getTimes->execute([
	$member,
	$type
]);
$times = $getTimes->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Add Manual Time: " . htmlspecialchars($row['MForename'] . " " . $row['MSurname']);
include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<h1>Add Manual Times for <?=htmlspecialchars($row['MForename'] . ' ' . $row['MSurname'])?></h1>
	<p class="lead">
		<?=htmlspecialchars($row['GalaName'])?>
	</p>

	<form method="post" class="">
		<h2>Swims</h2>

		<div>
		<?php for ($i = 0; $i < sizeof($swimsArray); $i++) { ?>
		<?php if ($row[$swimsArray[$i]] == 1) { ?>
		<div>
	    <div class="cell">
	      <strong class="d-block">
					<?=$swimsTextArray[$i]?>
				</strong>
				<?php if ($times[$swimsArray[$i]] != "") {
					echo $times[$swimsArray[$i]];
				} else if ($row[$swimsTimeArray[$i]] != "") {
					echo $row[$swimsTimeArray[$i]];
				} else {
					?>
				<div class="form-group mb-0">
					<div class="row no-gutters">
  			    <div class="col">
  			      <input type="number" class="form-control" placeholder="Minutes" name="<?=$swimsTimeArray[$i]?>Mins" id="<?=$swimsTimeArray[$i]?>Mins" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0">
  			    </div>
  					<div class="col">
  			      <input type="number" class="form-control" placeholder="Seconds" name="<?=$swimsTimeArray[$i]?>Secs" id="<?=$swimsTimeArray[$i]?>Secs" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="59">
  			    </div>
  					<div class="col">
  			      <input type="number" class="form-control" placeholder="Hundreds" name="<?=$swimsTimeArray[$i]?>Hunds" id="<?=$swimsTimeArray[$i]?>Hunds" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="99">
  			    </div>
  				</div>
  		  </div>
					<?php
				} ?>
	    </div>
		</div>
		<?php } ?>
		<?php } ?>
		</div>

		<p>
			<button class="btn btn-success" type="submit">Save</button>
		</p>
	</form>

</div>

<?php

include BASE_PATH . 'views/footer.php';
