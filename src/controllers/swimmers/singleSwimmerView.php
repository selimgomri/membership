<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

global $db;
$access = $_SESSION['AccessLevel'];

$markdown = new ParsedownExtra();
$markdown->setSafeMode(true);

$use_white_background = true;

$query = $db->prepare("SELECT * FROM members LEFT JOIN users ON members.UserID = users.UserID WHERE MemberID = ?");
$query->execute([$id]);
$row = $query->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$forename = $row['MForename'];
$middlename = $row['MMiddleNames'];
$surname = $row['MSurname'];
$dateOfBirth = $row['DateOfBirth'];
$sex = $row['Gender'];
$otherNotes = $row['OtherNotes'];

$parentEmail = $row['EmailAddress'];

$parent_id;

$mostRecentForms = $db->prepare("SELECT Form, `Date` FROM completedForms WHERE Member = ? ORDER BY `Date` DESC LIMIT 5");
$mostRecentForms->execute([$id]);
$mostRecentForm = $mostRecentForms->fetch(PDO::FETCH_ASSOC);

$query = $db->prepare("SELECT members.UserID, members.MForename, members.MForename, members.MMiddleNames,
members.MSurname, members.ASANumber, members.ASACategory, members.ClubPays,
squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable,
squads.SquadCoC, members.DateOfBirth, members.Gender, members.OtherNotes,
members.AccessKey, memberPhotography.Website, memberPhotography.Social,
memberPhotography.Noticeboard, memberPhotography.FilmTraining,
memberPhotography.ProPhoto, memberMedical.Conditions, memberMedical.Allergies,
memberMedical.Medication FROM (((members INNER JOIN squads ON members.SquadID =
squads.SquadID) LEFT JOIN `memberPhotography` ON members.MemberID =
memberPhotography.MemberID) LEFT JOIN `memberMedical` ON members.MemberID =
memberMedical.MemberID) WHERE members.MemberID = ?");
$query->execute([$id]);
$rowSwim = $query->fetch(PDO::FETCH_ASSOC);
$parent_id = $rowSwim['UserID'];

// Arrays of swims used to check whever to print the name of the swim entered
// BEWARE This is in an order to ease inputting data into SportSystems, contrary to these arrays in other files
$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$strokeCounts = [
  'Free' => 0,
  'Back' => 0,
  'Breast' => 0,
  'Fly' => 0,
  'IM' => 0
];
$distanceCounts = [
  '50' => 0,
  '100' => 0,
  '150' => 0,
  '200' => 0,
  '400' => 0,
  '800' => 0,
  '1500' => 0
];
$chartColours = chartColours(5);
$countEntries = [];
$countEntriesEvents = [];
$countEntriesCount = [];
$countEntriesColours = [];
foreach ($swimsArray as $col => $name) {
  $getCount = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE MemberID = ? AND `" . $col . "` = 1");
  $getCount->execute([$id]);
  $count = $getCount->fetchColumn();
  if ($count > 0) {
    $countEntries[$col]['Name'] = $name;
    $countEntriesEvents[] = html_entity_decode($name);
    $countEntries[$col]['Event'] = $col;
    $countEntries[$col]['Stroke'] = preg_replace("/[^a-zA-Z]+/", "", $col);
    $countEntries[$col]['Distance'] = preg_replace("/[^0-9]/", '', $col);
    $countEntries[$col]['Count'] = $count;
    $countEntriesCount[] = $count;
    $strokeCounts[$countEntries[$col]['Stroke']] += $countEntries[$col]['Count'];
    $distanceCounts[$countEntries[$col]['Distance']] += $countEntries[$col]['Count'];
    if ($countEntries[$col]['Stroke'] == 'Free') {
      $countEntriesColours[] = $chartColours[0];
    } else if ($countEntries[$col]['Stroke'] == 'Back') {
      $countEntriesColours[] = $chartColours[1];
    } else if ($countEntries[$col]['Stroke'] == 'Breast') {
      $countEntriesColours[] = $chartColours[2];
    } else if ($countEntries[$col]['Stroke'] == 'Fly') {
      $countEntriesColours[] = $chartColours[3];
    } else if ($countEntries[$col]['Stroke'] == 'IM') {
      $countEntriesColours[] = $chartColours[4];
    }
  }
}

$strokeCountsData = array_values($strokeCounts);

$pagetitle = htmlspecialchars($rowSwim['MForename'] . " " . $rowSwim['MSurname']) . " - Swimmer";
$age = date_diff(date_create($rowSwim['DateOfBirth']),
date_create('today'))->y;
$title = null;
$content = '
<div id="dash">
    <h1 class="">' . htmlspecialchars($rowSwim["MForename"]);
    if ($rowSwim["MMiddleNames"] != "") {
       $content .= ' ' . htmlspecialchars($rowSwim["MMiddleNames"]);
    }
    $content .= ' ' . htmlspecialchars($rowSwim["MSurname"]) . '
    <small>Member, ' . htmlspecialchars($rowSwim["SquadName"]) . ' Squad</small></h1>
</div>';
if ($parent_id != null) {
$content .= '
<p>
  <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Quick actions
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <a class="dropdown-item" href="' . autoUrl("members/" . $id . "/enter-gala") . '">Enter a gala</a>
      <a class="dropdown-item" href="' . htmlspecialchars('mailto:' . $parentEmail) . '">Email parent/guardian</a>';
      if ($_SESSION['AccessLevel'] != 'Galas') {
      $content .= '
      <a class="dropdown-item" href="' . autoUrl("members/" . $id . "/new-move") . '">New squad move</a>
      <a class="dropdown-item" href="' . autoUrl("members/" . $id . "/parenthelp") . '">Print access key</a>';
      }
      $content .= '
    </div>
  </div>
</p>
<p>Use the <strong>Quick actions</strong> menu to make gala entries, contact a parent/guardian, make a squad move and more.</p>';
}
if (isset($_SESSION['NotifyIndivSuccess'])) {
  if ($_SESSION['NotifyIndivSuccess']) {
    $content .= '<div class="alert alert-success">We\'ve sent an email to ' . htmlspecialchars($rowSwim["MForename"]) . '\'s parent.</div>';
  } else {
    $content .= '<div class="alert alert-warning">We could not send an email to ' . htmlspecialchars($rowSwim["MForename"]) . '\'s parent.</div>';
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
<div class="mb-3 card" id="about">
  <div class="card-body">
    <h2 class="mb-0">About ' . htmlspecialchars($rowSwim["MForename"]) . '</h2>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">';
    if ($_SESSION['AccessLevel'] != 'Galas') {
      $content .= '
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Date of Birth</strong>
        ' . date('j F Y', strtotime($rowSwim['DateOfBirth'])) . '
      </p>';
    } else {
      $today = new DateTime('now', new DateTimeZone('Europe/London'));
      $birthday = new DateTime($rowSwim['DateOfBirth'], new DateTimeZone('Europe/London'));
      $content .= '
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Age</strong>
        ' . $birthday->diff($today)->format('%y') . '
      </p>';
    }
    $content .= '
    </li>
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Swim England Number</strong>
        <a href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=' . htmlspecialchars($rowSwim["ASANumber"]) . '" target="_blank" title="ASA Biographical Data"><span class="mono">' . htmlspecialchars($rowSwim["ASANumber"]) . '</span> <i class="fa fa-external-link" aria-hidden="true"></i></a>
      </p>
    </li>
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Swim England Membership Category</strong>
        ' . htmlspecialchars($rowSwim["ASACategory"]) . '
      </p>
    </li>';
    if ($_SESSION['AccessLevel'] != 'Galas') {
    $content .= '
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Parent Account Setup
        Information</strong>
        <a href="' . autoUrl("members/" . $id . "/parenthelp") . '">Access Key for ' .
        htmlspecialchars($rowSwim["MForename"]) . '</a>
      </p>
    </li>';
    }
    if (bool(env('IS_CLS'))) {
    $content .= '
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Swimmer Membership Card</strong>
        <a href="' . autoUrl("members/" . $id . "/membershipcard") . '" target="_blank">Print Card</a>
      </p>
    </li>';
    }
    if ($_SESSION['AccessLevel'] != 'Galas') {
    $content .= '
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Attendance</strong>
        <a href="' . autoUrl("members/" . $id . "/attendance") . '">' .
        getAttendanceByID(null, $id, 4) . '% over the last 4 weeks, ' .
        getAttendanceByID(null, $id) . '% over all time</a>
      </p>
    </li>
    ';
    }
    $content .= '
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Sex</strong>
        ' . htmlspecialchars($rowSwim["Gender"]) . '
      </p>
    </li>';
    if ($access == "Admin" || $access == "Coach") {
    $content .= '
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Move Swimmer to New Squad</strong>
        <a href="' . autoUrl("members/" . $id . "/new-move") . '">New Move</a>
      </p>
    </li>';
    $content .= '
    <li class="list-group-item">
      <div class="mb-0">
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
    </li>
    ';
    }
    if ($rowSwim["OtherNotes"] != "") {
    $content .= '
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">Other Notes</strong>
        ' . htmlspecialchars($rowSwim["OtherNotes"]) . '
      </p>
    </li>';
    }
    if ($_SESSION['AccessLevel'] != 'Galas') {
    $content .= '
    <li class="list-group-item">
      <p class="mb-0">
        <strong class="d-block text-gray-dark">
          Exempt from Squad and Membership Fees?
        </strong>';
    if (bool($rowSwim["ClubPays"])){
      $content .= 'Yes';
    } else {
      $content .= 'No <em>(Only swimmers at University are usually exempt from most
      fees)</em>';
    }
    $content .= '
      </p>
    </li>';
  }
  $content .= '</ul><div class="card-body">';
	if ($access == "Admin") {
    $content .= '
	  <span class="d-block text-right d-print-none">
	    <a class="btn btn-success" href="' . autoUrl("members/" . $id . "/edit") . '">Edit Details</a> <a class="btn btn-success" href="' . autoUrl("members/" . $id . "/medical") . '">Edit Medical Notes</a>
	  </span>';
	} else {
		$content .= '
	  <span class="d-block d-print-none">
	    Please contact the parent or an administrator if you need to make changes to the details shown on this page.
	  </span>';
  }
  $content .= '</div></div>';
  if ($access != 'Galas') {
  $content .= '
  <div class="mb-3 card card-body" id="photo">
    <h2>Photography Permissions</h2>';
    if (($rowSwim['Website'] != 1 || $rowSwim['Social'] != 1 || $rowSwim['Noticeboard'] != 1 || $rowSwim['FilmTraining'] != 1 || $rowSwim['ProPhoto'] != 1) && ($age < 18)) {
      $content .= '
      <p>There are limited photography permissions for this swimmer</p>
      <ul class="mb-0">';
      if (!isset($row['Website']) || !$row['Website']) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our website</li>';
      }
      if (!isset($row['Social']) || !$row['Social']) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our social media</li>';
      }
      if (!isset($row['Noticeboard']) || !$row['Noticeboard']) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our noticeboard</li>';
      }
      if (!isset($row['FilmTraining']) || !$row['FilmTraining']) {
        $content .= '<li>This swimmer <strong>must not</strong> be filmed for the purposes of training</li>';
      }
      if (!isset($row['ProPhoto']) || !$row['ProPhoto']) {
        $content .= '<li>Photos <strong>must not</strong> be taken of this swimmer by photographers</li>';
      }
      $content .= '</ul>';
    } else {
      $content .= '<p class="mb-0">There are no photography limitiations for this swimmer. Please do ensure you\'ve read the club and Swim England policies on photography before taking any pictures.</p>';
    }
  $content .= '</div>';
  $query = $db->prepare("SELECT `Forename`, `Surname`, users.UserID, `Mobile` FROM `members` INNER JOIN `users` ON users.UserID = members.UserID WHERE `MemberID` = ?");
  $query->execute([$id]);
  $row = $query->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
  $mobile = PhoneNumber::parse($row['Mobile']);
  $content .= '
    <div class="mb-3 card" id="emergency">
      <div class="card-body">
      <h2>Emergency Contacts</h2>';
      if ($row == null) {
      $content .= '<p class="lead">
        There are no contact details available.
      </p>
      <p class="mb-0">This is because there is no Parent account connected</p></div>';
    } else {
      $contacts = new EmergencyContacts($db);
      $contacts->byParent($row['UserID']);
      $contactsArray = $contacts->getContacts();
      $content .= '<p class="lead mb-0">
        In an emergency you should try to contact
      </p></div>';
      $content .= '<ul class="list-group list-group-flush">';
      $content .= '<li class="list-group-item">
          <p class="mb-0">
            <strong class="d-block">
              ' . htmlspecialchars($row['Forename'] . ' ' . $row['Surname']) . ' (Account Parent)
            </strong>
            <a href="' . htmlspecialchars($mobile->format(PhoneNumberFormat::RFC3966)) . '">
              ' . htmlspecialchars($mobile->format(PhoneNumberFormat::NATIONAL)) . '
            </a>
          </p>
        </li>';
  		for ($i = 0; $i < sizeof($contactsArray); $i++) {
  			$content .= '<li class="list-group-item">
						<p class="mb-0">
							<strong class="d-block">
								' . htmlspecialchars($contactsArray[$i]->getName()) . '
							</strong>
							<a href="' . htmlspecialchars($contactsArray[$i]->getRFCContactNumber()) . '">
								' . htmlspecialchars($contactsArray[$i]->getNationalContactNumber()) . '
							</a>
						</p>
  				</li>';
      }
  		$content .= '</ul>';
      $content .= '<div class="card-body"><p class="mb-0">Make sure you know what to do in an emergency</p></div>';
    }
  $content .= '</div>';
  }
  }
  $content .= '</div>
  <div class="col-12 col-lg-8">';
  $content.= '
  <div class="mb-3 card card-body" id="times">
    <h2>Best Times</h2>
    <div class="alert alert-info">
      <p>
        <strong>Try our new best times system now!*</strong>
      </p>
      <p>
        <a href="' . htmlspecialchars(autoUrl("members/" . $id . "/times")) . '" class="btn btn-info">
          View times
        </a>
      </p>

      <p class="mb-0"><small>* Selected customer clubs only.</small></p>
    </div>';
    $mob = app('request')->isMobile();
    $timeGet = $db->prepare("SELECT * FROM `times` WHERE `MemberID` = ? AND `Type` = ?");
    $timeGet->execute([$id, 'SCPB']);
    $sc = $timeGet->fetch(PDO::FETCH_ASSOC);
    $timeGet->execute([$id, 'LCPB']);
    $lc = $timeGet->fetch(PDO::FETCH_ASSOC);
    $timeGet->execute([$id, 'CY_SC']);
    $scy = $timeGet->fetch(PDO::FETCH_ASSOC);
    $timeGet->execute([$id, 'CY_LC']);
    $lcy = $timeGet->fetch(PDO::FETCH_ASSOC);
    $ev = ['50Free', '100Free', '200Free', '400Free', '800Free', '1500Free',
    '50Breast', '100Breast', '200Breast', '50Fly', '100Fly', '200Fly',
    '50Back', '100Back', '200Back', '100IM', '200IM', '400IM'];
    $evs = ['50m Free', '100m Free', '200m Free', '400m Free', '800m Free', '1500m Free',
    '50m Breast', '100m Breast', '200m Breast', '50m Fly', '100m Fly', '200m Fly',
    '50m Back', '100m Back', '200m Back', '100m IM', '200m IM', '400m IM'];
    $content.= '<table class="table table-sm table-borderless table-striped mb-2">
    <thead class="thead-light"><tr><th>Swim</th><th>Short Course</th>';
    if (!$mob && $scy) {
      $content .= '<th>' . date("Y") . ' SC</th>';
    }
    $content .= '<th>Long Course</th>';
    if (!$mob && $lcy) {
      $content .= '<th>' . date("Y") . ' LC</th>';
    }
    $content .= '</thead>
    <tbody>';
    for ($i = 0; $i < sizeof($ev); $i++) {
    if ($sc[$ev[$i]] != "" || $lc[$ev[$i]] != "") {
      $content.= '<tr><td><strong>' . $evs[$i] . '</strong></td><td>';
      if ($sc[$ev[$i]] != "") {
        $content.= $sc[$ev[$i]];
      }
      if (!$mob && $scy) {
        $content .= '</td><td>' . $scy[$ev[$i]];
      }
      $content .= '</td><td>';
      if ($lc[$ev[$i]] != "") {
        $content.= $lc[$ev[$i]];
      }
      if (!$mob && $lcy) {
        $content .= '</td><td>' . $lcy[$ev[$i]];
      }
      $content.= '</td></tr>';
    }
    }
    $content.= '
    </tbody></table>
    <div>
      <div class="">
        <div class="row">
          <div class="col">
            <strong class="d-block text-gray-dark">View Online</strong>
            <a  href="https://www.swimmingresults.org/individualbest/personal_best.php?mode=A&tiref=' . htmlspecialchars($rowSwim["ASANumber"]) . '" target="_blank" title="Best Times">
              HTML
            </a>
          </div>
          <div class="col">
            <strong class="d-block text-gray-dark">Print or Download</strong>
            <a href="https://www.swimmingresults.org/individualbest/personal_best.php?print=2&mode=A&tiref=' . htmlspecialchars($rowSwim["ASANumber"]) . '" target="_blank" title="Best Times">
            PDF</a>
          </div>
        </div>
      </div>
    </div>
    <p class="mt-3 mb-0"><a href="' . htmlspecialchars(autoUrl("members/" . $id . "/edit-times")) . '" class="btn btn-primary">Edit times</a></p>
  </div>';

	if (sizeof($countEntries) > 0) {
	$content .= "
      <div class=\"mb-3 card card-body w-100\">
        <h2>Gala Statistics</h2>
        <div class=\"row\">
          <div class=\"col-lg-6\">
            <canvas id=\"strokeEntries\" class=\"mb-3\"></canvas>
          </div>
          <div class=\"col-lg-6\">
            <canvas id=\"eventEntries\" class=\"mb-3\"></canvas>
          </div>
        </div>
      </div>
	";
}
$content .= '
<div class="mb-3 card" id="squad">
<div class="card-body">
<h2 class="mb-0">Squad Information</h2>
</div>
<ul class="list-group list-group-flush">
<li class="list-group-item">
  <p class="mb-0">
    <strong class="d-block text-gray-dark">Squad</strong>
    ' . htmlspecialchars($rowSwim["SquadName"]) . ' Squad
  </p>
</li>
<li class="list-group-item">
  <p class="mb-0">
    <strong class="d-block text-gray-dark">Squad Fee</strong>';
    if ($rowSwim["ClubPays"] == 1) {
      $content .= $rowSwim['MForename'] . ' is Exempt from Squad Fees';
    } else {
      $content .= '&pound;' . number_format($rowSwim['SquadFee'], 2);
    }
    $content .= '
  </p>
</li>';
if ($rowSwim['SquadTimetable'] != "") {
  $content .= '
  <li class="list-group-item">
    <p class="mb-0">
      <strong class="d-block text-gray-dark">Squad Timetable</strong>
      <a href="' . htmlspecialchars($rowSwim["SquadTimetable"]) . '">Squad Timetable</a>
    </p>
  </li>';
}
if ($rowSwim['SquadCoC'] != "") {
  $content .= '
  <li class="list-group-item">
    <p class="mb-0">
      <strong class="d-block text-gray-dark">Squad Code of Conduct</strong>
      <a href="' . autoUrl("pages/codeofconduct/" . $rowSwim["SquadCoC"]) . '">Squad Code of Conduct</a>
    </p>
  </li>';
}
if ($rowSwim['SquadCoach'] != "") {
  $content .= '
  <li class="list-group-item">
    <p class="mb-0">
      <strong class="d-block text-gray-dark">Squad Coach</strong>
      ' . htmlspecialchars($rowSwim["SquadCoach"]) . '
    </p>
  </li>';
}
$content .= '</ul></div>';

if ($mostRecentForm != null) {
  $content .= '<div class="card"><div class="card-body"><h2 class="mb-0">Most Recent Returned Forms</h2></div><ul class="list-group list-group-flush">';
  do {
    $datetime = new DateTime($mostRecentForm['Date'], new DateTimeZone('UTC'));
    $datetime->setTimezone(new DateTimeZone('Europe/London'));
    $formDate = $datetime->format('l j F Y');
    $content .= '<li class="list-group-item"><strong>' . htmlspecialchars($mostRecentForm['Form']) . '</strong>, Returned ' . $formDate . '</li>';
  } while ($mostRecentForm = $mostRecentForms->fetch(PDO::FETCH_ASSOC));
}

$content .= '</ul></div>';

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
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("members")?>">Members</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($rowSwim["MForename"])?> <?=htmlspecialchars(mb_substr($rowSwim["MSurname"], 0, 1, 'utf-8'))?></li>
    </ol>
  </nav>

  <?php if (isset($_SESSION['SwimmerAdded']) && $_SESSION['SwimmerAdded']) { ?>
  <div class="alert alert-success">
    <p class="mb-0">
      <strong>Swimmer added successfully</strong>
    </p>
    <p class="mb-0">
      <a href="<?=autoUrl("members/new")?>" class="alert-link">Add another swimmer</a> or proceed to <a href="<?=autoUrl("assisted-registration")?>" class="alert-link">assisted registration</a>
    </p>
  </div>
  <?php unset($_SESSION['SwimmerAdded']); } ?>

<?= $content ?>
</div>

<?php if (sizeof($countEntries) > 0) { ?>
<script src="<?=autoUrl("public/js/Chart.min.js")?>"></script>
<script>
var ctx = document.getElementById('eventEntries').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'bar',

  // The data for our dataset
  data: {
    labels: <?=json_encode($countEntriesEvents)?>,
    datasets: [{
      label: <?=json_encode($rowSwim['MForename'] . " " . $rowSwim['MSurname'])?>,
      data: <?=json_encode($countEntriesCount)?>,
      backgroundColor: <?=json_encode($countEntriesColours)?>,
    }],
  },

  // Configuration options go here
  options: {
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
          precision: 0,
        }
      }]
    }
  }
});
</script>

<script>
var ctx = document.getElementById('strokeEntries').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'pie',

  // The data for our dataset
  data: {
    labels: <?=json_encode(['Free', 'Back', 'Breast', 'Fly', 'IM'])?>,
    datasets: [{
      label: <?=json_encode(html_entity_decode($gala['GalaName']))?>,
      data: <?=json_encode($strokeCountsData)?>,
      backgroundColor: <?=json_encode($chartColours)?>,
    }],
  },

  // Configuration options go here
  // options: {}
});
</script>
<?php } ?>

<?php include BASE_PATH . "views/footer.php";