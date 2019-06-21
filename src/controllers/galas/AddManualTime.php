<?php

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50&nbsp;Free','100&nbsp;Free','200&nbsp;Free','400&nbsp;Free','800&nbsp;Free','1500&nbsp;Free','50&nbsp;Back','100&nbsp;Back','200&nbsp;Back','50&nbsp;Breast','100&nbsp;Breast','200&nbsp;Breast','50&nbsp;Fly','100&nbsp;Fly','200&nbsp;Fly','100&nbsp;IM','150&nbsp;IM','200&nbsp;IM','400&nbsp;IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','150IMTime','200IMTime','400IMTime',];

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM ((`galaEntries` INNER JOIN `members` ON
`members`.`MemberID` = `galaEntries`.`MemberID`) INNER JOIN `galas` ON
galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$member = mysqli_real_escape_string($link, $row['MemberID']);

$type = null;
if ($row['CourseLength'] == "SHORT") {
	$type = "SCPB";
} else {
	$type = "LCPB";
}
$times = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `times`
WHERE `MemberID` = '$member' AND `Type` = '$type';"), MYSQLI_ASSOC);

$pagetitle = "Add Manual Time: " . $row['MForename'] . " " . $row['MSurname'];
include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<h1>Add Manual Times for <?= $row['MForename'] ?> <?= $row['MSurname'] ?></h1>
	<p class="lead">
		<?php echo $row['GalaName']; ?>
	</p>

	<form method="post" class="my-3 p-3 bg-white rounded shadow">
		<h2 class="pb-2 mb-0">Swims</h2>

		<div class="mb-3">
		<?php for ($i = 0; $i < sizeof($swimsArray); $i++) { ?>
		<?php if ($row[$swimsArray[$i]] == 1) { ?>
		<div class="media pt-3">
	    <div class="media-body pt-3 mb-0 lh-125 border-top border-gray">
	      <strong class="d-block">
					<?php echo $swimsTextArray[$i]; ?>
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
  			      <input type="number" class="form-control" placeholder="Minutes" name="<?php echo $swimsTimeArray[$i]; ?>Mins" id="<?php echo $swimsTimeArray[$i]; ?>Mins" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0">
  			    </div>
  					<div class="col">
  			      <input type="number" class="form-control" placeholder="Seconds" name="<?php echo $swimsTimeArray[$i]; ?>Secs" id="<?php echo $swimsTimeArray[$i]; ?>Secs" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="59">
  			    </div>
  					<div class="col">
  			      <input type="number" class="form-control" placeholder="Hundreds" name="<?php echo $swimsTimeArray[$i]; ?>Hunds" id="<?php echo $swimsTimeArray[$i]; ?>Hunds" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="99">
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

		<p class="mb-0">
			<button class="btn btn-success" type="submit">Save</button>
		</p>
	</form>

</div>

<?php

include BASE_PATH . 'views/footer.php';
