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

if ($_SESSION['AccessLevel'] == 'Parent' && $row['UserID'] != $_SESSION['UserID']) {
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

$pagetitle = "Add manual times for " . htmlspecialchars($row['MForename'][0] . $row['MSurname'][0] . ' at ' . $row['GalaName']);
include BASE_PATH . 'views/header.php';

$course = 'short';
$altCourse = 'long';
if ($row['CourseLength'] == 'LONG') {
	$course = 'long';
	$altCourse = 'short';
}

?>

<div class="container">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("galas/entries")?>">Entries</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("galas/entries/" . $row['EntryID'])?>"><?=htmlspecialchars(mb_substr($row["MForename"], 0, 1, 'utf-8') . mb_substr($row["MSurname"], 0, 1, 'utf-8'))?> (<?=htmlspecialchars($row['GalaName'])?>)</a></li>
			<li class="breadcrumb-item active" aria-current="page">Times</li>
		</ol>
	</nav>


	<h1>Add Manual Times for <?=htmlspecialchars($row['MForename'] . ' ' . $row['MSurname'])?></h1>
	<p class="lead">
		<?=htmlspecialchars($row['GalaName'])?>
	</p>

	<div class="row">
		<div class="col-lg-8">

			<?php if (isset($_SESSION['UpdateSuccess'])) {
				if ($_SESSION['UpdateSuccess']) { ?>
				<div class="alert alert-success">
					<p class="mb-0">
						<strong>
							Your manual times have been saved successfully.
						</strong>
					</p>
				</div>
				<?php } else { ?>
				<div class="alert alert-danger">
					<p class="mb-0">
						<strong>
							We were unable to save your manual times.
						</strong>
					</p>
					<p class="mb-0">
						Please try again. If the issue persists, contact support.
					</p>
				</div>
				<?php }
				unset($_SESSION['UpdateSuccess']);
			} ?>

			<p>
				Please enter <?=$course?> course times. If you have no <?=$course?> course time for an event but do have a <?=$altCourse?> course time, please <a href="<?=autoUrl("timeconverter")?>" target="_blank">use our online time converter (opens in new tab)</a> to convert it from <?=$altCourse?> course to <?=$course?> course.
			</p>

			<p>
				If you do not have any short course or long course times for an event, leave it blank.
			</p>

			<form method="post" class="">
				<h2>Swims</h2>
				<p class="lead">
					These are the events you entered.
				</p>

				<div>
				<?php for ($i = 0; $i < sizeof($swimsArray); $i++) { ?>
				<?php if ($row[$swimsArray[$i]] == 1) { ?>
				<div>
					<div class="cell">
						<strong class="d-block">
							<label>
								<?=$swimsTextArray[$i]?>
							</label>
						</strong>
						<?php if ($times[$swimsArray[$i]] != "") {
							echo $times[$swimsArray[$i]];
						} else {
							$matches = $mins = $secs = $hunds = null;
							if ($row[$swimsTimeArray[$i]] != "") {
								if (preg_match('/([0-9]+)\:([0-9]{0,2})\.([0-9]{0,2})/', $row[$swimsTimeArray[$i]], $matches)) {
									if (isset($matches[1]) && $matches[1] != 0) {
										$mins = $matches[1];
									}
									if (isset($matches[2]) && $matches[2] != 0) {
										$secs = $matches[2];
									}
									if (isset($matches[3]) && $matches[3] != 0) {
										$hunds = $matches[3];
									}
								}
							}
							?>
						<div class="form-group mb-0 mt-2">
							<div class="input-group">
								<input type="number" class="form-control" placeholder="Minutes" name="<?=$swimsTimeArray[$i]?>Mins" id="<?=$swimsTimeArray[$i]?>Mins" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" value="<?=htmlspecialchars($mins)?>">
								<input type="number" class="form-control" placeholder="Seconds" name="<?=$swimsTimeArray[$i]?>Secs" id="<?=$swimsTimeArray[$i]?>Secs" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="59" value="<?=htmlspecialchars($secs)?>">
								<input type="number" class="form-control" placeholder="Hundreds" name="<?=$swimsTimeArray[$i]?>Hunds" id="<?=$swimsTimeArray[$i]?>Hunds" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="99" value="<?=htmlspecialchars($hunds)?>">
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
	</div>

</div>

<?php

$footer = new \SDCS\Footer();
$footer->render();
