<?php

global $db;

$swimmer = $db->prepare("SELECT MForename, MSurname, UserID FROM members WHERE MemberID = ?");
$swimmer->execute([$id]);
$swimmer = $swimmer->fetch(PDO::FETCH_ASSOC);

if ($swimmer == null) {
  halt(404);
}

if ($_SESSION['AccessLevel'] == 'Parent' && $swimmer['UserID'] !== $_SESSION['UserID']) {
	halt(404);
}

$pagetitle = htmlspecialchars($swimmer['MForename'] . ' ' . $swimmer['MSurname']) . ' Times';

$getTimes = $db->prepare("SELECT * FROM `times` WHERE `MemberID` = ? AND `Type` = ?;");
$getTimes->execute([
	$id,
	'SCPB'
]);
$timesSC = $getTimes->fetch(PDO::FETCH_ASSOC);
$getTimes->execute([
	$id,
	'LCPB'
]);
$timesLC = $getTimes->fetch(PDO::FETCH_ASSOC);

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','200IM','400IM',];
$swimsTextArray = ['50&nbsp;Free','100&nbsp;Free','200&nbsp;Free','400&nbsp;Free','800&nbsp;Free','1500&nbsp;Free','50&nbsp;Back','100&nbsp;Back','200&nbsp;Back','50&nbsp;Breast','100&nbsp;Breast','200&nbsp;Breast','50&nbsp;Fly','100&nbsp;Fly','200&nbsp;Fly','100&nbsp;IM','200&nbsp;IM','400&nbsp;IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','200IMTime','400IMTime',];

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("swimmers")?>">Swimmers</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("swimmers/" . $id)?>"><?=htmlspecialchars($swimmer["MForename"])?> <?=htmlspecialchars(mb_substr($swimmer["MSurname"], 0, 1, 'utf-8'))?></a></li>
			<li class="breadcrumb-item active" aria-current="page">Edit times</li>
		</ol>
	</nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Edit times for <?=htmlspecialchars($swimmer['MForename'] . ' ' . $swimmer['MSurname'])?></h1>

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

    </div>
  </div>

  <form action="" method="post">

    <div class="row">
      <div class="col-md-6">

        <h2>Short Course</h2>

      <?php for ($i = 0; $i < sizeof($swimsArray); $i++) { ?>
				<div>
					<div class="cell">
						<strong class="d-block">
							<label>
								<?=$swimsTextArray[$i]?>
							</label>
						</strong>
            <?php
							$matches = $mins = $secs = $hunds = null;
							if ($timesSC[$swimsArray[$i]] != "") {
								if (preg_match('/([0-9]+)\:([0-9]{0,2})\.([0-9]{0,2})/', $timesSC[$swimsArray[$i]], $matches)) {
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
								<input type="number" class="form-control" placeholder="Minutes" name="<?=$swimsTimeArray[$i]?>SCMins" id="<?=$swimsTimeArray[$i]?>SCMins" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" value="<?=htmlspecialchars($mins)?>">
								<input type="number" class="form-control" placeholder="Seconds" name="<?=$swimsTimeArray[$i]?>SCSecs" id="<?=$swimsTimeArray[$i]?>SCSecs" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="59" value="<?=htmlspecialchars($secs)?>">
								<input type="number" class="form-control" placeholder="Hundreds" name="<?=$swimsTimeArray[$i]?>SCHunds" id="<?=$swimsTimeArray[$i]?>SCHunds" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="99" value="<?=htmlspecialchars($hunds)?>">
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
      </div>
      
      <div class="col-md-6">

        <h2>Long Course</h2>

      <?php for ($i = 0; $i < sizeof($swimsArray); $i++) { ?>
				<div>
					<div class="cell">
						<strong class="d-block">
							<label>
								<?=$swimsTextArray[$i]?>
							</label>
						</strong>
            <?php
							$matches = $mins = $secs = $hunds = null;
							if ($timesLC[$swimsArray[$i]] != "") {
								if (preg_match('/([0-9]+)\:([0-9]{0,2})\.([0-9]{0,2})/', $timesLC[$swimsArray[$i]], $matches)) {
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
								<input type="number" class="form-control" placeholder="Minutes" name="<?=$swimsTimeArray[$i]?>LCMins" id="<?=$swimsTimeArray[$i]?>LCMins" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" value="<?=htmlspecialchars($mins)?>">
								<input type="number" class="form-control" placeholder="Seconds" name="<?=$swimsTimeArray[$i]?>LCSecs" id="<?=$swimsTimeArray[$i]?>LCSecs" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="59" value="<?=htmlspecialchars($secs)?>">
								<input type="number" class="form-control" placeholder="Hundreds" name="<?=$swimsTimeArray[$i]?>LCHunds" id="<?=$swimsTimeArray[$i]?>LCHunds" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="99" value="<?=htmlspecialchars($hunds)?>">
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
      </div>
    </div>

    <p>
      <button class="btn btn-success" type="submit">
        Save
      </button>
    </p>

  </form>
</div>

<?php

include BASE_PATH . 'views/footer.php';