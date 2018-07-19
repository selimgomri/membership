<?php
$id = mysqli_real_escape_string($link, $id);
$access = $_SESSION['AccessLevel'];

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

$sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames,
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
$pagetitle = "Swimmer: " . $rowSwim['MForename'] . " " . $rowSwim['MSurname'];
$age = date_diff(date_create($rowSwim['DateOfBirth']),
date_create('today'))->y;
$title = null;
$content = '
<div class="d-flex align-items-center p-3 my-3 text-white bg-primary rounded box-shadow" id="dash">
  <img class="mr-3" src="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png" alt="" width="48" height="48">
  <div class="lh-100">
    <h1 class="h6 mb-0 text-white lh-100">' . $rowSwim["MForename"];
    if ($rowSwim["MMiddleNames"] != "") {
       $content .= ' ' . $rowSwim["MMiddleNames"];
    }
    $content .= ' ' . $rowSwim["MSurname"] . '</h1>
    <small>Swimmer, ' . $rowSwim["SquadName"] . ' Squad</small>
  </div>
</div>
<div class="my-3 p-3 bg-white rounded box-shadow">
  <h2 class="border-bottom border-gray pb-2 mb-0">About ' . $rowSwim["MForename"] . '</h2>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Date of Birth</strong>
      ' . date('j F Y', strtotime($rowSwim['DateOfBirth'])) . '
    </p>
  </div>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">ASA Number</strong>
      <a href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=' . $rowSwim["ASANumber"] . '" target="_blank" title="ASA Biographical Data"><span class="mono">' . $rowSwim["ASANumber"] . '</span> <i class="fa fa-external-link" aria-hidden="true"></i></a>
    </p>
  </div>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">ASA Membership Category</strong>
      ' . $rowSwim["ASACategory"] . ' <em>(This is not yet accurate)</em>
    </p>
  </div>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Parent Account Setup
      Information</strong>
      <a href="' . autoUrl("swimmers/parenthelp/" . $id) . '">Access Key for ' .
      $rowSwim["MForename"] . '</a>
    </p>
  </div>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Attendance</strong>
      <a href="' . autoUrl("attendance/history/swimmers/" . $id) . '">' .
      getAttendanceByID($link, $id, 4) . '% over the last 4 weeks, ' .
      getAttendanceByID($link, $id) . '% over all time</a>
    </p>
  </div>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Sex</strong>
      ' . $rowSwim["Gender"] . '
    </p>
  </div>';
  if ($access == "Admin" || $access == "Committee" || $access == "Coach") {
    $content .= '
    <div class="media pt-3">
      <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark">Move Swimmer to New Squad</strong>
        <a href="' . autoUrl("squads/moves/new/" . $id) . '">New Move<a>
      </p>
    </div>';
  }
  $content .= '
  <div class="media pt-3">
    <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <p class="mb-0 text-gray-dark">
        <strong>
          Medical Notes
        </strong>
      </p>

      <p class="mb-0 mt-2">
        <em>
          Medical Conditions or Disabilities
        </em>
      </p>';
      if ($rowSwim["Conditions"] != "") {
        $content .= '
        <p class="mb-0">';
        $content .= $rowSwim["Conditions"];
        $content .= '</p>';
      } else {
        $content .= '<p class="mb-0">None</p>';
      }

      $content .= '<p class="mb-0 mt-2">
        <em>
          Allergies
        </em>
      </p>';
      if ($rowSwim["Allergies"] != "") {
        $content .= '<p class="mb-0">';
        $content .= $rowSwim["Allergies"];
        $content .= '</p>';
      } else {
        $content .= '<p class="mb-0">None</p>';
      }

      $content .= '<p class="mb-0 mt-2">
        <em>
          Medication
        </em>
      </p>';
      if ($rowSwim["Medication"] != "") {
        $content .= '<p class="mb-0">';
        $content .= $rowSwim["Medication"];
        $content .= '</p>';
      } else {
        $content .= '<p class="mb-0">None</p>';
      }

    $content .= '</div>
  </div>
  ';
  if ($rowSwim["OtherNotes"] != "") {
    $content .= '
    <div class="media pt-3">
      <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark">Other Notes</strong>
        ' . $rowSwim["OtherNotes"] . '
      </p>
    </div>';
  }
  $content .= '
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
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
	  <span class="d-block text-right mt-3">
	    <a href="edit/' . $id . '">Edit Details</a> or <a href="' . $id . '/medical">add Medical Notes</a>
	  </span>
		</div>';
	}
	else {
		$content .= '
	  <span class="d-block text-right mt-3">
	    Please contact a Parent or Administrator if you need to make changes
	  </span>
		</div>';
	}
$content .= '
  <div class="my-3 p-3 bg-white rounded box-shadow">
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
      $content .= '<p class="mb-0">There are no photography limitiations for this swimmer. Please do ensure you\'ve read the club and ASA policies on photography before taking any pictures.</p>';
    }
  $content .= '</div>';
  $sql = "SELECT `Forename`, `Surname`, users.UserID, `Mobile` FROM `members`
  INNER JOIN `users` ON users.UserID = members.UserID WHERE `MemberID` =
  '$id';";
  $result = mysqli_query($link, $sql);
  $content .= '
    <div class="my-3 p-3 bg-white rounded box-shadow">
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
      $content .= '<div class="media pt-3">
        <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
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
  			$content .= '<div class="media pt-3">
  				<div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
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
  $content .= '</div>';
  $content.= '
  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h2 class="border-bottom border-gray pb-2 mb-0">Best Times</h2>
    <div class="media pt-3">
      <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark">View Online</strong>
        <a href="https://www.swimmingresults.org/individualbest/personal_best.php?mode=A&tiref=' . $rowSwim["ASANumber"] . '" target="_blank" title="Best Times">
        HTML</a>
      </p>
    </div>
    <div class="media pt-3">
      <p class="media-body mb-0 lh-125">
        <strong class="d-block text-gray-dark">Print or Download</strong>
        <a href="https://www.swimmingresults.org/individualbest/personal_best.php?print=2&mode=A&tiref=' . $rowSwim["ASANumber"] . '" target="_blank" title="Best Times">
        PDF</a>
      </p>
    </div>
  </div>';
/* Stats Section */
$swimsCountArray = [];
$strokesCountArray = [0, 0, 0, 0, 0];
$strokesCountTextArray = ["Freestyle", "Breaststroke", "Butterfly", "Backstroke", "Individual Medley"];
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
      <div class=\"my-3 p-3 bg-white rounded box-shadow\">
      <h2 class=\"border-bottom border-gray pb-2 mb-0\">Gala Statistics</h2>
	    <div id=\"piechart\"></div>
			<div id=\"barchart\"></div>
      </div>
	";
}
$content .= '
<div class="my-3 p-3 bg-white rounded box-shadow">
<h2 class="border-bottom border-gray pb-2 mb-0">Squad Information</h2>
<div class="media pt-3">
  <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
    <strong class="d-block text-gray-dark">Squad</strong>
    ' . $rowSwim["SquadName"] . ' Squad
  </p>
</div>
<div class="media pt-3">
  <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
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
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Squad Timetable</strong>
      <a href="' . $rowSwim["SquadTimetable"] . '">Squad Timetable</a>
    </p>
  </div>';
}
if ($rowSwim['SquadCoC'] != "") {
  $content .= '
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <strong class="d-block text-gray-dark">Squad Code of Conduct</strong>
      <a href="' . $rowSwim["SquadCoC"] . '">Squad Code of Conduct</a>
    </p>
  </div>';
}
if ($rowSwim['SquadCoach'] != "") {
  $content .= '
  <div class="media pt-3 mb-0">
    <p class="media-body pb-3 mb-0 lh-125">
      <strong class="d-block text-gray-dark">Squad Coach</strong>
      ' . $rowSwim["SquadCoach"] . '
    </p>
  </div>';
}
$content .= '</div>';

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
