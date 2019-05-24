<?php
$id = mysqli_real_escape_string($link, $id);
$access = $_SESSION['AccessLevel'];

$markdown = new ParsedownExtra();
$markdown->setSafeMode(true);

$use_white_background = true;

$query = "SELECT * FROM members WHERE MemberID = '$id' ";
$result = mysqli_query($link, $query);
if (mysqli_num_rows($result) != 1) {
  halt(404);
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$forename = $row['MForename'];
$middlename = $row['MMiddleNames'];
$surname = $row['MSurname'];
$dateOfBirth = $row['DateOfBirth'];
$sex = $row['Gender'];
$otherNotes = $row['OtherNotes'];

$parent_id;

$sqlSwim = "SELECT members.UserID, members.MForename, members.MForename, members.MMiddleNames,
members.MSurname, members.ASANumber, members.ASACategory, members.ClubPays,
squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable,
squads.SquadCoC, members.DateOfBirth, members.Gender, members.OtherNotes,
members.AccessKey, memberPhotography.Website, memberPhotography.Social,
memberPhotography.Noticeboard, memberPhotography.FilmTraining,
memberPhotography.ProPhoto, memberMedical.Conditions, memberMedical.Allergies,
memberMedical.Medication FROM (((members INNER JOIN squads ON members.SquadID =
squads.SquadID) LEFT JOIN `memberPhotography` ON members.MemberID =
memberPhotography.MemberID) LEFT JOIN `memberMedical` ON members.MemberID =
memberMedical.MemberID) WHERE members.MemberID = '$id';";
$resultSwim = mysqli_query($link, $sqlSwim);
$rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
$parent_id = $rowSwim['UserID'];
$pagetitle = $rowSwim['MForename'] . " " . $rowSwim['MSurname'] . " - Swimmer";
$age = date_diff(date_create($rowSwim['DateOfBirth']),
date_create('today'))->y;
$title = null;
$content = '
<div id="dash">
    <h1 class="">' . $rowSwim["MForename"];
    if ($rowSwim["MMiddleNames"] != "") {
       $content .= ' ' . $rowSwim["MMiddleNames"];
    }
    $content .= ' ' . $rowSwim["MSurname"] . '
    <small>Swimmer, ' . $rowSwim["SquadName"] . ' Squad</small></h1>
</div>';
if ($parent_id != null) {
$content .= '
<p><a target="_self" href="' . autoUrl("swimmers/" . $id . "/contactparent") . '">Contact ' . $rowSwim["MForename"] . '\'s parent/guardian by email</a></p>';
}
if (isset($_SESSION['NotifyIndivSuccess'])) {
  if ($_SESSION['NotifyIndivSuccess']) {
    $content .= '<div class="alert alert-success">We\'ve sent an email to ' . $rowSwim["MForename"] . '\'s parent.</div>';
  } else {
    $content .= '<div class="alert alert-warning">We could not send an email to ' . $rowSwim["MForename"] . '\'s parent.</div>';
  }
  unset($_SESSION['NotifyIndivSuccess']);
}
$content .= '<!--
<ul class="nav nav-pills d-print-none">
  <li class="nav-item">
    <a class="nav-link" href="#about">About ' . $rowSwim["MForename"] . '</a>
  </li>';
  if ($age < 18) {
    $content .= '
    <li class="nav-item">
      <a class="nav-link" href="#photo">Photography Permissions</a>
    </li>';
  }
  $content .= '
  <li class="nav-item">
    <a class="nav-link" href="#emergency">Emergency Contacts</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#times">Best Times</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#squad">Squad Information</a>
  </li>
</ul>-->
<div class="row justify-content-center mt-3">
  <div class="col-12 col-lg-4">
<div class="mb-3 cell" id="about">
  <h2 class="border-bottom border-gray pb-2 mb-0">About ' . $rowSwim["MForename"] . '</h2>
  <div class="media pt-2">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Date of Birth</strong>
      ' . date('j F Y', strtotime($rowSwim['DateOfBirth'])) . '
    </p>
  </div>
  <div class="media pt-2">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Swim England Number</strong>
      <a href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=' . $rowSwim["ASANumber"] . '" target="_blank" title="ASA Biographical Data"><span class="mono">' . $rowSwim["ASANumber"] . '</span> <i class="fa fa-external-link" aria-hidden="true"></i></a>
    </p>
  </div>
  <div class="media pt-2">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Swim England Membership Category</strong>
      ' . $rowSwim["ASACategory"] . '
    </p>
  </div>
  <div class="media pt-2 d-print-none">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Parent Account Setup
      Information</strong>
      <a href="' . autoUrl("swimmers/" . $id . "/parenthelp") . '">Access Key for ' .
      $rowSwim["MForename"] . '</a>
    </p>
  </div>';
  if (defined("IS_CLS") && IS_CLS) {
  $content .= '
  <div class="media pt-2 d-print-none">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Swimmer Membership Card</strong>
      <a href="' . autoUrl("swimmers/" . $id . "/membershipcard") . '" target="_blank">Print Card</a>
    </p>
  </div>';
  }
  $content .= '
  <div class="media pt-2">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Attendance</strong>
      <a href="' . autoUrl("swimmers/" . $id . "/attendance") . '">' .
      getAttendanceByID($link, $id, 4) . '% over the last 4 weeks, ' .
      getAttendanceByID($link, $id) . '% over all time</a>
    </p>
  </div>
  <div class="media pt-2">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Sex</strong>
      ' . $rowSwim["Gender"] . '
    </p>
  </div>';
  if ($access == "Admin" || $access == "Committee" || $access == "Coach") {
    $content .= '
    <div class="media pt-2">
      <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark">Move Swimmer to New Squad</strong>
        <a href="' . autoUrl("swimmers/" . $id . "/new-move") . '">New Move</a>
      </p>
    </div>';
  }
  $content .= '
  <div class="media pt-2">
    <div class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <h3>
        Medical Notes
      </h3>

      <h4>
        Medical Conditions or Disabilities
      </h4>';
      if ($rowSwim["Conditions"] != "") {
        $content .= $markdown->text($rowSwim["Conditions"]);
      } else {
        $content .= '<p>None</p>';
      }

      $content .= '
      <h4>
        Allergies
      </h4>';
      if ($rowSwim["Allergies"] != "") {
        $content .= $markdown->text($rowSwim["Allergies"]);
      } else {
        $content .= '<p>None</p>';
      }

      $content .= '
      <h4>
        Medication
      </h4>';
      if ($rowSwim["Medication"] != "") {
        $content .= $markdown->text($rowSwim["Medication"]);
      } else {
        $content .= '<p>None</p>';
      }

    $content .= '</div>
  </div>
  ';
  if ($rowSwim["OtherNotes"] != "") {
    $content .= '
    <div class="media pt-2">
      <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark">Other Notes</strong>
        ' . $rowSwim["OtherNotes"] . '
      </p>
    </div>';
  }
  $content .= '
  <div class="media pt-2">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">
        Exempt from Squad and Membership Fees?
      </strong>';
  if ($rowSwim["ClubPays"] == 1){
    $content .= 'Yes';
  } else {
    $content .= 'No <em>(Only swimmers at University are usually exempt from most
    fees)</em>';
  }
  $content .= '
    </p>
  </div>';
	if ($access == "Admin" || $access == "Committee") {
	  $content .= '
	  <span class="d-block text-right mt-3 d-print-none">
	    <a class="btn btn-success" href="' . autoUrl("swimmers/" . $id . "/edit") . '">Edit Details</a> <a class="btn btn-success" href="' . autoUrl("swimmers/" . $id . "/medical") . '">Edit Medical Notes</a>
	  </span>
		</div>';
	}
	else {
		$content .= '
	  <span class="d-block text-right mt-3 d-print-none">
	    Please contact a Parent or Administrator if you need to make changes
	  </span>
		</div>';
	}
$content .= '
  <div class="mb-3 cell" id="photo">
    <h2 class="border-bottom border-gray pb-2 mb-2">Photography Permissions</h2>';
    if (($rowSwim['Website'] != 1 || $rowSwim['Social'] != 1 || $rowSwim['Noticeboard'] != 1 || $rowSwim['FilmTraining'] != 1 || $rowSwim['ProPhoto'] != 1) && ($age < 18)) {
      $content .= '
      <p>There are limited photography permissions for this swimmer</p>
      <ul class="mb-0">';
      if ($row['Website'] != 1) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our website</li>';
      }
      if ($row['Social'] != 1) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our social media</li>';
      }
      if ($row['Noticeboard'] != 1) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our noticeboard</li>';
      }
      if ($row['FilmTraining'] != 1) {
        $content .= '<li>This swimmer <strong>must not</strong> be filmed for the purposes of training</li>';
      }
      if ($row['ProPhoto'] != 1) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer by photographers</li>';
      }
      $content .= '</ul>';
    } else {
      $content .= '<p class="mb-0">There are no photography limitiations for this swimmer. Please do ensure you\'ve read the club and Swim England policies on photography before taking any pictures.</p>';
    }
  $content .= '</div>';
  $sql = "SELECT `Forename`, `Surname`, users.UserID, `Mobile` FROM `members`
  INNER JOIN `users` ON users.UserID = members.UserID WHERE `MemberID` =
  '$id';";
  $result = mysqli_query($link, $sql);
  $content .= '
    <div class="mb-3 cell" id="emergency">
      <h2>Emergency Contacts</h2>';
      if (mysqli_num_rows($result) == 0) {
      $content .= '<p class="lead">
        There are no contact details available.
      </p>
      <p class="mb-0">This is because there is no Parent account connected</p>';
    } else {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $pUserID = mysqli_real_escape_string($link, $row['UserID']);
      $contacts = new EmergencyContacts($link);
      $contacts->byParent($pUserID);
      $contactsArray = $contacts->getContacts();
      $content .= '<p class="lead border-bottom border-gray pb-2 mb-0">
        In an emergency you should try to contact
      </p>';
      $content .= '<div class="mb-3">';
      $content .= '<div class="media pt-2">
        <div class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
          <p class="mb-0">
            <strong class="d-block">
              ' . $row['Forename'] . ' ' . $row['Surname'] . ' (Account Parent)
            </strong>
            <a href="tel:' . $row['Mobile'] . '">
              ' . $row['Mobile'] . '
            </a>
          </p>
        </div>
      </div>';
  		for ($i = 0; $i < sizeof($contactsArray); $i++) {
  			$content .= '<div class="media pt-2">
  				<div class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
						<p class="mb-0">
							<strong class="d-block">
								' . $contactsArray[$i]->getName() . '
							</strong>
							<a href="tel:' . $contactsArray[$i]->getContactNumber() . '">
								' . $contactsArray[$i]->getContactNumber() . '
							</a>
						</p>
  				</div>
  			</div>';
      }
  		$content .= '</div>';
      $content .= '<p class="mb-0">Make sure you understand the Emergency Operating Procedures</p>';
    }
  $content .= '</div></div>
  <div class="col-12 col-lg-8">';
  $content.= '
  <div class="mb-3 cell" id="times">
    <h2 class="border-bottom border-gray pb-2 mb-2">Best Times</h2>';
    $mob = app('request')->isMobile();
    $sc = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'SCPB';";
    $lc = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'LCPB';";
    $scy = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'CY_SC';";
    $lcy = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'CY_LC';";
    $sc = mysqli_fetch_array(mysqli_query($link, $sc), MYSQLI_ASSOC);
    $lc = mysqli_fetch_array(mysqli_query($link, $lc), MYSQLI_ASSOC);
    $scy = mysqli_fetch_array(mysqli_query($link, $scy), MYSQLI_ASSOC);
    $lcy = mysqli_fetch_array(mysqli_query($link, $lcy), MYSQLI_ASSOC);
    $ev = ['50Free', '100Free', '200Free', '400Free', '800Free', '1500Free',
    '50Breast', '100Breast', '200Breast', '50Fly', '100Fly', '200Fly',
    '50Back', '100Back', '200Back', '100IM', '200IM', '400IM'];
    $evs = ['50m Free', '100m Free', '200m Free', '400m Free', '800m Free', '1500m Free',
    '50m Breast', '100m Breast', '200m Breast', '50m Fly', '100m Fly', '200m Fly',
    '50m Back', '100m Back', '200m Back', '100m IM', '200m IM', '400m IM'];
    $content.= '<table class="table table-sm table-borderless table-striped mb-2">
    <thead class="thead-light"><tr class="pl-0"><th class="pl-0">Swim</th><th>Short Course</th>';
    if (!$mob) {
      $content .= '<th>SC: Last 12 Months</th>';
    }
    $content .= '<th>Long Course</th>';
    if (!$mob) {
      $content .= '<th>LC: Last 12 Months</th>';
    }
    $content .= '</thead>
    <tbody>';
    for ($i = 0; $i < sizeof($ev); $i++) {
    if ($sc[$ev[$i]] != "" || $lc[$ev[$i]] != "") {
      $content.= '<tr class="pl-0"><td class="pl-0"><strong>' . $evs[$i] . '</strong></td><td>';
      if ($sc[$ev[$i]] != "") {
        $content.= $sc[$ev[$i]];
      }
      if (!$mob) {
        $content .= '</td><td>' . $scy[$ev[$i]];
      }
      $content .= '</td><td>';
      if ($lc[$ev[$i]] != "") {
        $content.= $lc[$ev[$i]];
      }
      if (!$mob) {
        $content .= '</td><td>' . $lcy[$ev[$i]];
      }
      $content.= '</td></tr>';
    }
    }
    $content.= '
    </tbody></table>
    <div class="media d-print-none border-top border-gray pt-2">
      <div class="media-body pb-0 mb-0 lh-125">
        <div class="row">
          <div class="col">
            <strong class="d-block text-gray-dark">View Online</strong>
            <a  href="https://www.swimmingresults.org/individualbest/personal_best.php?mode=A&tiref=' . $rowSwim["ASANumber"] . '" target="_blank" title="Best Times">
              HTML
            </a>
          </div>
          <div class="col">
            <strong class="d-block text-gray-dark">Print or Download</strong>
            <a href="https://www.swimmingresults.org/individualbest/personal_best.php?print=2&mode=A&tiref=' . $rowSwim["ASANumber"] . '" target="_blank" title="Best Times">
            PDF</a>
          </div>
        </div>
      </div>
    </div>
  </div>';
/* Stats Section */
$swimsCountArray = [];
$strokesCountArray = [0, 0, 0, 0, 0];
$strokesCountTextArray = ["Free", "Breast", "Fly", "Back", "IM"];
$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$strokesArray = ['0','0','0','0','0','0','1','1','1','2','2','2','3','3','3','4','4','4','4',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$counter = 0;
for ($i=0; $i<sizeof($swimsArray); $i++) {
	$col = $swimsArray[$i];
	$sql = "SELECT `$col` FROM `galaEntries` WHERE `MemberID` = '$id' AND `$col` = '1'";
	$result = mysqli_query($link, $sql);
	$count = mysqli_num_rows($result);
	$swimsCountArray[$i] = $count;
	$strokesCountArray[$strokesArray[$i]] += $count;
	$counter += $count;
}
	if ($counter>0) {
	$content .= "<script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
	    <script type=\"text/javascript\">
	      google.charts.load('current', {'packages':['corechart']});

	      google.charts.setOnLoadCallback(drawPieChart);
				google.charts.setOnLoadCallback(drawBarChart);

	      function drawPieChart() {

	        var data = google.visualization.arrayToDataTable([
	          ['Stroke', 'Total Number of Entries'],";
						for ($i=0; $i<sizeof($strokesCountArray); $i++) {
	          	$content .= "['" . $strokesCountTextArray[$i] . "', " . $strokesCountArray[$i] . "],";
						}
	        $content .= "]);

	        var options = {
	          title: 'Gala Entries by Stroke',
						fontName: 'Open Sans',
						backgroundColor: {
							fill:'transparent'
						},
						chartArea: {
							left: '0',
							right: '0',
						}
	        };

	        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

	        chart.draw(data, options);
	      }
				function drawBarChart() {

	        var data = google.visualization.arrayToDataTable([
	          ['Stroke', 'Total Number of Entries'],";
						for ($i=0; $i<sizeof($swimsArray); $i++) {
							if ($swimsCountArray[$i] > 0) {
	          		$content .= "['" . $swimsTextArray[$i] . "', " . $swimsCountArray[$i] . "],";
							}
						}
	        $content .= "]);

	        var options = {
	          title: 'Gala Entries by Event',
						fontName: 'Open Sans',
						backgroundColor: {
							fill:'transparent'
						},
						chartArea: {
							left: '0',
							right: '0',
						},
						backgroundColor: {
							fill:'transparent'
						},
						legend: {
							position: 'none',
						}
	        };

	        var chart = new google.visualization.ColumnChart(document.getElementById('barchart'));

	        chart.draw(data, options);
	      }
	    </script>
      <div class=\"mb-3 cell w-100\">
        <h2 class=\"border-bottom border-gray pb-2 mb-0\">Gala Statistics</h2>
  	    <div class=\"w-100\" id=\"piechart\"></div>
  			<div class=\"w-100\" id=\"barchart\"></div>
      </div>
	";
}
$content .= '
<div class="mb-3 cell" id="squad">
<h2 class="border-bottom border-gray pb-2 mb-0">Squad Information</h2>
<div class="media pt-2">
  <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
    <strong class="d-block text-gray-dark">Squad</strong>
    ' . $rowSwim["SquadName"] . ' Squad
  </p>
</div>
<div class="media pt-2">
  <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
    <strong class="d-block text-gray-dark">Squad Fee</strong>';
    if ($rowSwim["ClubPays"] == 1) {
      $content .= $rowSwim['MForename'] . ' is Exempt from Squad Fees';
    } else {
      $content .= '&pound;' . $rowSwim['SquadFee'];
    }
    $content .= '
  </p>
</div>';
if ($rowSwim['SquadTimetable'] != "") {
  $content .= '
  <div class="media pt-2 d-print-none">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Squad Timetable</strong>
      <a href="' . $rowSwim["SquadTimetable"] . '">Squad Timetable</a>
    </p>
  </div>';
}
if ($rowSwim['SquadCoC'] != "") {
  $content .= '
  <div class="media pt-2 d-print-none">
    <p class="media-body pb-2 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Squad Code of Conduct</strong>
      <a href="' . autoUrl("pages/codeofconduct/" . $rowSwim["SquadCoC"]) . '">Squad Code of Conduct</a>
    </p>
  </div>';
}
if ($rowSwim['SquadCoach'] != "") {
  $content .= '
  <div class="media pt-2 mb-0">
    <p class="media-body pb-2 mb-0 lh-125">
      <strong class="d-block text-gray-dark">Squad Coach</strong>
      ' . $rowSwim["SquadCoach"] . '
    </p>
  </div>';
}
$content .= '</div></div></div>';

$fluidContainer = true;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<style>
.px48 {
  width: 48px;
  height: 48px;
}
.max48 {
  max-height: 48px;
  width: auto;
}
</style>
<div class="container-fluid">
<?= $content ?>
</div>
<?php include BASE_PATH . "views/footer.php";
