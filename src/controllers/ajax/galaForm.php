<?php

global $db;

$count = 0;
$rows = 0;
$sql = "";
$response = "";

$coachEnters = false;
// Check if coach enters
if (isset($_GET["galaID"])) {
	$getCoachEnters = $db->prepare("SELECT CoachEnters FROM galas WHERE GalaID = ?");
	$getCoachEnters->execute([$_GET["galaID"]]);
	$coachEnters = bool($getCoachEnters->fetchColumn());
}

if (!$coachEnters && (isset($_REQUEST["galaID"])) && (isset($_REQUEST["swimmer"]))) {
  // get the galaID parameter from request
  $galaID = $_REQUEST["galaID"];
	$memberID = $_REQUEST["swimmer"];
	
	// Get swimmer info
	$getSwimmer = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, DateOfBirth dob, UserID parent FROM members WHERE MemberID = ?");
	$getSwimmer->execute([
	  $_GET['swimmer']
	]);
	$swimmer = $getSwimmer->fetch(PDO::FETCH_ASSOC);

	if ($swimmer == null || ($_SESSION['AccessLevel'] == 'Parent' && $swimmer['parent'] != $_SESSION['UserID'])) {
		halt(404);
	}

	// Get gala info
	$getGala = $db->prepare("SELECT GalaFeeConstant flatfee, GalaFee fee, HyTek, GalaName `name`, GalaVenue venue FROM galas WHERE GalaID = ?");
	$getGala->execute([
		$_GET["galaID"]
	]);
	$gala = $getGala->fetch(PDO::FETCH_ASSOC);

	if ($gala == null) {
		halt(404);
	}

  $existing = $db->prepare("SELECT * FROM galaEntries WHERE GalaID = ? AND MemberID = ?");
  $existing->execute([$galaID, $memberID]);

  $row = $existing->fetch(PDO::FETCH_ASSOC);

  if ($row != null) {
    $response = '<div class="alert alert-warning"><strong>Oops. You\'ve aleady entered this swimmer into this gala</strong> <br>
    You might want to check that. ';
    if ($row['EntryProcessed'] == 0) {
      $response .= 'We\'ve not processed your entry yet, so you <a class="alert-link" href="' . autoUrl("galas/entries/" . $row["EntryID"]) . '">can edit your gala entry</a> if you need to make changes.';
    } else {
      $response .= 'We\'ve already processed your gala entry - You\'ll need to contact your gala administrator if you need to make any chnages.';
    }
    $response .= '</div>';
  }
  else {

    $details = $db->prepare("SELECT `HyTek`, `GalaName`, `GalaFeeConstant` FROM galas WHERE GalaID = ?");
    $details->execute([$galaID]);
    $row = $details->fetch(PDO::FETCH_ASSOC);

  	$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
  	$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
  	$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
    $rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
    $rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

		$response .= "<h2>Select Swims</h2>
		<p>All swims possible under Swim England Rules are shown below. Not all of these
		events may be available for " . $row['GalaName'] . "</p>";

    if ($row['HyTek'] == 1) {
      $response .= '<p>This is a HyTek gala. Once you\'ve selected your swims, you\'ll need to provide times for each event.</p>';
    }

    $response .= "
	  <div class=\"row mb-3\">
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Free\" name=\"50Free\">
	      <label class=\"custom-control-label\" for=\"50Free\">50 Freestyle</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Free\" name=\"100Free\">
	      <label class=\"custom-control-label\" for=\"100Free\">100 Freestyle</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Free\" name=\"200Free\">
	      <label class=\"custom-control-label\" for=\"200Free\">200 Freestyle</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"400Free\" name=\"400Free\">
	      <label class=\"custom-control-label\" for=\"400Free\">400 Freestyle</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"800Free\" name=\"800Free\">
	      <label class=\"custom-control-label\" for=\"800Free\">800 Freestyle</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"1500Free\" name=\"1500Free\">
	      <label class=\"custom-control-label\" for=\"1500Free\">1500 Freestyle</label>
	    </div>
	    </div>
	  </div>
	  <div class=\"row mb-3\">
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Breast\" name=\"50Breast\">
	      <label class=\"custom-control-label\" for=\"50Breast\">50 Breaststroke</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Breast\" name=\"100Breast\">
	      <label class=\"custom-control-label\" for=\"100Breast\">100 Breaststroke</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Breast\" name=\"200Breast\">
	      <label class=\"custom-control-label\" for=\"200Breast\">200 Breaststroke</label>
	    </div>
	    </div>
	  </div>
	  <div class=\"row mb-3\">
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Fly\" name=\"50Fly\">
	      <label class=\"custom-control-label\" for=\"50Fly\">50 Butterfly</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Fly\" name=\"100Fly\">
	      <label class=\"custom-control-label\" for=\"100Fly\">100 Butterfly</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Fly\" name=\"200Fly\">
	      <label class=\"custom-control-label\" for=\"200Fly\">200 Butterfly</label>
	    </div>
	    </div>
	  </div>
	  <div class=\"row mb-3\">
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Back\" name=\"50Back\">
	      <label class=\"custom-control-label\" for=\"50Back\">50 Backstroke</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Back\" name=\"100Back\">
	      <label class=\"custom-control-label\" for=\"100Back\">100 Backstroke</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Back\" name=\"200Back\">
	      <label class=\"custom-control-label\" for=\"200Back\">200 Backstroke</label>
	    </div>
	    </div>
	  </div>
	  <div class=\"row mb-3\">
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100IM\" name=\"100IM\">
	      <label class=\"custom-control-label\" for=\"100IM\">100 IM</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"150IM\" name=\"150IM\">
	      <label class=\"custom-control-label\" for=\"150IM\">150 IM</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200IM\" name=\"200IM\">
	      <label class=\"custom-control-label\" for=\"200IM\">200 IM</label>
	    </div>
	    </div>
	    <div class=\"col-sm-4 col-md-2\">
	    <div class=\"custom-control custom-checkbox\">
	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"400IM\" name=\"400IM\">
	      <label class=\"custom-control-label\" for=\"400IM\">400 IM</label>
	    </div>
	    </div>
	  </div>";

    if ($row['GalaFeeConstant'] != 1) {
      $response .= '
      <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="form-group">
            <label for="galaFee">Enter Total</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">&pound;</span>
              </div>
              <input aria-describedby="feeHelp" type="text" id="galaFee" name="galaFee" class="form-control" required>
            </div>
            <small id="feeHelp" class="form-text text-muted">Sadly we can\'t automatically calculate the entry fee for this gala so we need you to tell us.</small>
          </div>
        </div>
      </div>';
    }

  }

	echo $response;

} else if ($coachEnters && isset($_GET["galaID"]) && isset($_GET["swimmer"])) {

	/**
	 * This is a gala where the coach enters, so we will show
	 * the select available sessions interface.
	 */

	// Get swimmer info
	$getSwimmer = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, DateOfBirth dob, UserID parent FROM members WHERE MemberID = ?");
	$getSwimmer->execute([
	  $_GET['swimmer']
	]);
	$swimmer = $getSwimmer->fetch(PDO::FETCH_ASSOC);

	if ($swimmer == null || ($_SESSION['AccessLevel'] == 'Parent' && $swimmer['parent'] != $_SESSION['UserID'])) {
		halt(404);
	}

	// Get gala info
	$getGala = $db->prepare("SELECT GalaFeeConstant flatfee, GalaFee fee, HyTek, GalaName `name`, GalaVenue venue FROM galas WHERE GalaID = ?");
	$getGala->execute([
		$_GET["galaID"]
	]);
	$gala = $getGala->fetch(PDO::FETCH_ASSOC);

	if ($gala == null) {
		halt(404);
	}

	$nowDate = new DateTime('now', new DateTimeZone('Europe/London'));

	$getSessions = $db->prepare("SELECT `Name`, `ID` FROM galaSessions WHERE Gala = ? ORDER BY `ID` ASC");
	$getSessions->execute([$_GET["galaID"]]);
	$sessions = $getSessions->fetchAll(PDO::FETCH_ASSOC);

	$getCanAttend = $db->prepare("SELECT `Session`, `CanEnter` FROM galaSessionsCanEnter ca INNER JOIN galaSessions gs ON ca.Session = gs.ID WHERE gs.Gala = ? AND ca.Member = ?");
	$getCanAttend->execute([
		$_GET["galaID"],
		$_GET['swimmer']
	]);

	// Output
	?>
		<h2>Select available sessions</h2>
		<p class="lead">Select sessions which <?=htmlspecialchars($swimmer['fn'])?> will be able to swim at.</p>
		<p>Your coaches will use this information when making suggested entries to this gala.</p>

		<?php if ($sessions == null) { ?>
		<div class="alert alert-danger">
			<p class="mb-0"><strong>You cannot complete this form at this time.</strong></p>
			<p class="mb-0">Please contact your club.</p>
		</div>
		<?php } else {
			$canAtt = $getCanAttend->fetchAll(PDO::FETCH_KEY_PAIR);
			$checked = [];
			for ($i = 0; $i < sizeof($sessions); $i++) {
				if (isset($canAtt[$sessions[$i]['ID']]) && $canAtt[$sessions[$i]['ID']]) {
					$checked[] = " checked ";
				} else {
					$checked[] = "";
				}
			}

		?>

		<input type="hidden" name="is-select-sessions" value="1">

		<!--
		<h2><?=htmlspecialchars($swimmer['fn'] . ' ' . $swimmer['sn'])?></h2>
		<p class="lead"><?=htmlspecialchars($swimmer['fn'])?> is able to enter;</p>
		-->
		<div class="row">
		<?php for ($i = 0; $i < sizeof($sessions); $i++) { ?>
		<div class="col-sm-6 col-lg-4 col-xl-3">
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="<?=$swimmer['id']?>-<?=$sessions[$i]['ID']?>" name="<?=$swimmer['id']?>-<?=$sessions[$i]['ID']?>" <?=$checked[$i]?>>
					<label class="custom-control-label" for="<?=$swimmer['id']?>-<?=$sessions[$i]['ID']?>">
						<?=htmlspecialchars($sessions[$i]['Name'])?>
					</label>
				</div>
			</div>
		</div>
		<?php } ?>
		</div>
		<?php } ?>

	 	<?php if (bool($gala['flatfee'])) { ?>
		<p>When your coach completes entries, we will automatically work out the fee you'll need to pay. The fee for each entry is &pound;<?=number_format($gala['fee'], 2, '.', '')?>.</p>
		<?php } else { ?>
		<p>As some swims at this gala cost different amounts to other swims, your entry fee will be calculated manually by the coach who selects <?=htmlspecialchars($swimmer['fn'])?>'s swims. You should verify this amount when you are sent an email detailing which swims a coach has entered <?=htmlspecialchars($swimmer['fn'])?> for.</p>
		<?php } ?>

		<p>
			You should pay for entries to this gala in the usual way. Your club has not provided guidance as to which payment methods are accepted, which would be displayed in place of this message. This system provides support for payments by card, account balance (paid off by direct debit or any other method supported by your club), 
		</p>

	 <?php

} else {
	halt(404);
}