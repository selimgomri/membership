<?php

$db = app()->db;
$tenant = app()->tenant;

/** must go in form
if (app('request')->method == 'POST' && !SCDS\CSRF::verify()) {
	halt(403);
}
 */

// Get all countries
$countries = getISOAlpha2CountriesWithHomeNations();

$access = $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'];
// Committee or Admin can see and change all data
$forenameUpdate = false;
$middlenameUpdate = false;
$surnameUpdate = false;
$asaUpdate = false;
$userUpdate = false;
$squadUpdate = false;
$dateOfBirthUpdate = false;
$sexUpdate = false;
$otherNotesUpdate = false;
$catUpdate = $cpUpdate = $sepUpdate = $clubCatUpdate = false;
$update = false;
$countryUpdate = false;
$swimmerStatusUpdate = false;
$successInformation = "";

$query = $db->prepare("SELECT * FROM members WHERE MemberID = ? AND Tenant = ?");
$query->execute([
	$id,
	$tenant->getId()
]);
$row = $query->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
	halt(404);
}

$forename = $row['MForename'];
$middlename = $row['MMiddleNames'];
$surname = $row['MSurname'];
$asaNumber = $row['ASANumber'];
$dbUserID = "";
$dbUserID = $row['UserID'];
$dateOfBirth = $row['DateOfBirth'];
$sex = $row['Gender'];
$otherNotes = $row['OtherNotes'];
$dbAccessKey = $row['AccessKey'];
$cat = $row['ASACategory'];
$swimmerStatus = $row['Status'];
$cp = $row['ClubPaid'];
$sep = $row['ASAPaid'];
$clubCat = $row['ClubCategory'];

if (!empty($_POST['forename'])) {
	$newForename = trim(mb_ucfirst($_POST['forename']));
	if ($newForename != $forename) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `MForename` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newForename, $id]);
		$forenameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['middlenames'])) {
	$newMiddlenames = trim(mb_ucfirst($_POST['middlenames']));
	if ($newMiddlenames != $middlename) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `MMiddleNames` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newMiddlenames, $id]);
		$middlenameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['surname'])) {
	$newSurname = trim(mb_ucfirst($_POST['surname']));
	if ($newSurname != $surname) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `MSurname` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newSurname, $id]);
		$surnameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['asa'])) {
	$newASANumber = trim(mb_ucfirst($_POST['asa']));
	if ($newASANumber != $asaNumber) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `ASANumber` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newASANumber, $id]);
		$asaUpdate = true;
		$update = true;
	}
} else if (isset($_POST['asa']) && $_POST['asa'] == "" && app('request')->method == "post") {
	$newASANumber = app()->tenant->getKey('ASA_CLUB_CODE') . $id;
	if ($newASANumber != $asaNumber) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `ASANumber` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newASANumber, $id]);
		$asaUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['userid'])) {
	$newUserID = trim($_POST['userid']);
	if ($newUserID != $dbUserID) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `UserID` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newUserID, $id]);
		$userUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['squad'])) {
	$newSquadID = trim($_POST['squad']);
	if ($newSquadID != $squad) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `SquadID` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newSquadID, $id]);
		$squadUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['datebirth'])) {
	$newDateOfBirth = trim($_POST['datebirth']);
	// NEEDS WORK FOR DATE TO BE RIGHT
	if ($newDateOfBirth != $dateOfBirth) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `DateOfBirth` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newDateOfBirth, $id]);
		$dateOfBirthUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['sex'])) {
	$newSex = trim($_POST['sex']);
	if ($newSex != $sex) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `Gender` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newSex, $id]);
		$sexUpdate = true;
		$update = true;
	}
}
if (isset($_POST['cat'])) {
	$newCat = trim($_POST['cat']);
	if ($newCat != $cat && ($newCat == 0 || $newCat == 1 || $newCat == 2 || $newCat == 3)) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `ASACategory` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newCat, $id]);
		$catUpdate = true;
		$update = true;
	}
}
if (isset($_POST['cp'])) {
	$newCp = trim($_POST['cp']);
	if ($newCp != $cp && ($newCp == 0 || $newCp == 1)) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `ClubPaid` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newCp, $id]);
		$cpUpdate = true;
		$update = true;
	}
}
if (isset($_POST['sep'])) {
	$newCp = trim($_POST['sep']);
	if ($newCp != $sep && ($newCp == 0 || $newCp == 1)) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `ASAPaid` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newCp, $id]);
		$sepUpdate = true;
		$update = true;
	}
}
if (isset($_POST['club-cat'])) {
	$getCats = $db->prepare("SELECT `ID` FROM `clubMembershipClasses` WHERE `Tenant` = ? ORDER BY `Name` ASC");
	$getCats->execute([
		$tenant->getId()
	]);
	$cats = $getCats->fetchAll(PDO::FETCH_COLUMN);
	if (in_array($_POST['club-cat'], $cats) && $_POST['club-cat'] != $clubCat) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `ClubCategory` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$_POST['club-cat'], $id]);
		$clubCatUpdate = true;
		$update = true;
	}
}
if (isset($_POST['otherNotes'])) {
	$newOtherNotes = trim($_POST['otherNotes']);
	if ($newOtherNotes != $otherNotes) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `OtherNotes` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newOtherNotes, $id]);
		$otherNotesUpdate = true;
		$update = true;
	}
}
if (isset($_POST['swimmerStatus']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") {
	$newSwimmerStatus = trim($_POST['swimmerStatus']);
	if ($newSwimmerStatus != $swimmerStatus) {
		$updateSwimmer = $db->prepare("UPDATE `members` SET `Status` = ? WHERE `MemberID` = ?");
		$updateSwimmer->execute([$newSwimmerStatus, $id]);
		$swimmerStatusUpdate = true;
		$update = true;
	}
}
if (isset($_POST['country'])) {

	if ($row['Country'] != $_POST['country'] && isset($countries[$_POST['country']])) {
		// Update
		$updateCountry = $db->prepare("UPDATE members SET Country = ? WHERE MemberID = ?");
		$updateCountry->execute([
			$_POST['country'],
			$id
		]);
		$countryUpdate = true;
		$update = true;
	}
}

$getClubCategories = $db->prepare("SELECT `ID`, `Name` FROM `clubMembershipClasses` WHERE `Tenant` = ? ORDER BY `Name` ASC");
$getClubCategories->execute([
	$tenant->getId()
]);
$clubCategory = $getClubCategories->fetch(PDO::FETCH_ASSOC);

AuditLog::new('Members-Edited', 'Edited ' . $forename . ' ' . $surname . ' (#' . $id . ')');

$sqlSwim = "";
$swimmer = $db->prepare("SELECT members.MForename, members.MForename, members.MMiddleNames,
members.MSurname, members.ASANumber, members.ASACategory, members.ASAPaid, members.ClubPaid, members.DateOfBirth, members.Gender,
members.OtherNotes, members.AccessKey, members.Status, members.Country, members.ClubCategory FROM members WHERE members.MemberID = ?");
$swimmer->execute([$id]);
$rowSwim = $swimmer->fetch(PDO::FETCH_ASSOC);
$pagetitle = "Swimmer: " . htmlspecialchars($rowSwim['MForename'] . " " . $rowSwim['MSurname']);
$title = null;
$content = '<form method="post" id="edit-form">';
$content .= "<div class=\"row\"><div class=\"col col-md-8\"><div class=\"\">";
if ($update) {
	$content .= '<div class="alert alert-success">
	<strong>We have updated</strong>
	<ul class="mb-0">';
	if ($forenameUpdate) {
		$content .= '<li>First name</li>';
	}
	if ($middlenameUpdate) {
		$content .= '<li>Middle name(s)</li>';
	}
	if ($surnameUpdate) {
		$content .= '<li>Last address</li>';
	}
	if ($dateOfBirthUpdate) {
		$content .= '<li>Date of birth</li>';
	}
	if ($asaUpdate) {
		$content .= '<li>Swim England Number</li>';
	}
	if ($userUpdate) {
		$content .= '<li>Parent</li>';
	}
	if ($squadUpdate) {
		$content .= '<li>Squad</li>';
	}
	if ($sexUpdate) {
		$content .= '<li>Sex</li>';
	}
	if ($catUpdate) {
		$content .= '<li>Swim England Category</li>';
	}
	if ($clubCatUpdate) {
		$content .= '<li>Club Membership Category</li>';
	}
	if ($cpUpdate) {
		$content .= '<li>Whether or not the club pays the member\'s
		Club Membership fees</li>';
	}
	if ($sepUpdate) {
		$content .= '<li>Whether or not the club pays the member\'s
		Swim England fees</li>';
	}
	if ($otherNotesUpdate) {
		$content .= '<li>Other notes</li>';
	}
	if ($swimmerStatusUpdate) {
		$content .= '<li>Swimmer Membership Status</li>';
	}
	if ($countryUpdate) {
		$content .= '<li>Home nations meet country</li>';
	}
	$content .= '
	</ul>
</div>';
}
$content .= "
<div class=\"mb-3\">
	<label for=\"forename\">Forename</label>
	<input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" value=\"" . htmlspecialchars($rowSwim['MForename']) . "\" required>
</div>";
$content .= "
<div class=\"mb-3\">
	<label for=\"middlenames\">Middle Names</label>
	<input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\" value=\"" . htmlspecialchars($rowSwim['MMiddleNames']) . "\">
</div>";
$content .= "
<div class=\"mb-3\">
	<label for=\"surname\">Surname</label>
	<input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" value=\"" . htmlspecialchars($rowSwim['MSurname']) . "\" required>
</div>";
$content .= "
<div class=\"mb-3\">
	<label for=\"datebirth\">Date of Birth</label>
	<input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" placeholder=\"YYYY-MM-DD\" value=\"" . htmlspecialchars($rowSwim['DateOfBirth']) . "\" required>
</div>";
$content .= "
<div class=\"mb-3\">
	<label for=\"asa\">Swim England Registration Number</label>
	<input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\" placeholder=\"Swim England Registration Numer\" value=\"" . htmlspecialchars($rowSwim['ASANumber']) . "\">
</div>";
$cat = ['', '', '', ''];
$cat[$rowSwim['ASACategory']] = " selected ";
$content .= "
<div class=\"mb-3\">
	<label for=\"cat\">Swim England Membership Category</label>
	<select class=\"form-select\" id=\"cat\" name=\"cat\" placeholder=\"Select\">
		<option value=\"0\" " . $cat[0] . ">Not a Swim England Member</option>
		<option value=\"1\" " . $cat[1] . ">Category 1</option>
		<option value=\"2\" " . $cat[2] . ">Category 2</option>
		<option value=\"3\" " . $cat[3] . ">Category 3</option>
	</select>
</div>";

// $clubCategory = $getClubCategories->fetch(PDO::FETCH_ASSOC)
$content .= "
<div class=\"mb-3\">
	<label for=\"club-cat\">Club Membership Category</label>
	<select class=\"form-select\" id=\"club-cat\" name=\"club-cat\" placeholder=\"Select\" required>";
do {
	$selected = '';
	if ($rowSwim['ClubCategory'] == $clubCategory['ID']) {
		$selected = ' selected ';
	}
	$content .= "<option value=\"" . htmlspecialchars($clubCategory['ID']) . "\" " . $selected . ">" . htmlspecialchars($clubCategory['Name']) . "</option>";
} while ($clubCategory = $getClubCategories->fetch(PDO::FETCH_ASSOC));
$content .= "
	</select>
</div>";

if ($rowSwim['Gender'] == "Male") {
	$content .= "
	<div class=\"mb-3\">
		<label for=\"sex\">Sex</label>
		<select class=\"form-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
			<option value=\"Male\" selected>Male</option>
			<option value=\"Female\">Female</option>
		</select>
	</div>";
} else {
	$content .= "
	<div class=\"mb-3\">
		<label for=\"sex\">Sex</label>
		<select class=\"form-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
			<option value=\"Male\">Male</option>
			<option value=\"Female\" selected>Female</option>
		</select>
	</div>";
}

$content .= "
<div class=\"mb-3\">
	<label for=\"country\">Home Nations Country</label>
	<select class=\"form-select\" id=\"country\" name=\"country\" placeholder=\"Select\">";
foreach ($countries as $key => $value) {
	$selected = '';
	if ($rowSwim['Country'] == $key) {
		$selected = ' selected ';
	}
	$content .= "<option value=\"" . htmlspecialchars($key) . "\" " . $selected . ">" . htmlspecialchars($value) . "</option>";
}
$content .= "
	</select>
</div>";

$cp = [
	'',
	''
];
$cp[$rowSwim['ASAPaid']] = " selected ";
$content .= "
<div class=\"mb-3\">
	<label for=\"sep\">Club pays Swim England fees?</label>
	<select class=\"form-select\" id=\"sep\" name=\"sep\" placeholder=\"Select\">
		<option value=\"0\" " . $cp[0] . ">No</option>
		<option value=\"1\" " . $cp[1] . ">Yes</option>
	</select>
</div>";

$cp = [
	'',
	''
];
$cp[$rowSwim['ClubPaid']] = " selected ";
$content .= "
<div class=\"mb-3\">
	<label for=\"cp\">Club pays Club Membership fees?</label>
	<select class=\"form-select\" id=\"cp\" name=\"cp\" placeholder=\"Select\">
		<option value=\"0\" " . $cp[0] . ">No</option>
		<option value=\"1\" " . $cp[1] . ">Yes</option>
	</select>
</div>";

$content .= '<div class="mb-3"> <label>Medical Notes</label>';
$content .= '<a class="d-block" href="' . autoUrl("members/" . $id . "/medical") . '"
target="_self">Edit medical notes</a>';
$content .= '</div>';
$content .= "
<div class=\"mb-3\">
	<label for=\"otherNotes\">Other Notes</label>
	<textarea class=\"form-control\" id=\"otherNotes\" name=\"otherNotes\" rows=\"3\" placeholder=\"Tell us any other notes for coaches\">" . htmlspecialchars($rowSwim['OtherNotes']) . "</textarea>
</div>";
if ($access == "Admin") {
	$statusA = '';
	$statusB = '';

	if ($rowSwim['Status']) {
		$statusA = "selected";
	} else {
		$statusB = "selected";
	}
	$content .= "
		<div class=\"mb-3\">
			<label for=\"swimmerStatus\">Swimmer Membership Status</label>
			<select class=\"form-select\" id=\"swimmerStatus\" name=\"swimmerStatus\" aria-describedby=\"swimmerStatusHelp\">
        <option value=\"1\" " . $statusA . ">Active</option>
        <option value=\"0\" " . $statusB . ">Suspended</option>
      </select>
			<small id=\"swimmerStatusHelp\" class=\"form-text\">Suspended swimmers will not show on registers.</small>
		</div>";
}
$content .= "<p><button type=\"submit\" class=\"btn btn-success rounded\">Update</button></p>";
$content .= "</div></div>";
$content .= "</div></form>";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<div class="bg-light mt-n3 py-3 mb-3">
	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= autoUrl("members") ?>">Members</a></li>

				<li class="breadcrumb-item"><a href="<?= autoUrl("members/" . $id) ?>"><?= htmlspecialchars($rowSwim["MForename"]) ?> <?= htmlspecialchars(mb_substr($rowSwim["MSurname"], 0, 1, 'utf-8')) ?></a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit</li>
			</ol>
		</nav>

		<div class="row align-items-center">
			<div class="col-md-8">
				<h1>Editing <?= htmlspecialchars($rowSwim['MForename'] . ' ' . $rowSwim['MSurname']) ?> </h1>
			</div>
			<div class="col text-md-end">
				<button type="submit" class="btn btn-success" form="edit-form">Update</button>
				<a class="btn btn-dark" href="<?= htmlspecialchars(autoUrl("members/$id")) ?>">Exit Edit Mode</a>
			</div>
		</div>
	</div>
</div>

<div class="container">

	<?php echo $content; ?>

</div>

<div class="container">
	<div class="row">
		<div class="col-lg-8">
			<div class="cell">
				<h2>Delete member</h2>
				<p>
					<button data-ajax-url="<?= htmlspecialchars(autoUrl("members/delete")) ?>" data-members-url="<?= htmlspecialchars(autoUrl("members")) ?>" data-member-id="<?= htmlspecialchars($id) ?>" data-member-name="<?= htmlspecialchars($rowSwim['MForename'] . ' ' . $rowSwim['MSurname']) ?>" id="delete-button" class="btn btn-danger">
						Delete account
					</button>
				</p>
			</div>
		</div>
	</div>
</div>


<!-- Modal for use by JS code -->
<div class="modal fade" id="main-modal" tabindex="-1" role="dialog" aria-labelledby="main-modal-title" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="main-modal-title">Modal title</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					
				</button>
			</div>
			<div class="modal-body" id="main-modal-body">
				...
			</div>
			<div class="modal-footer" id="main-modal-footer">
				<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
				<button type="button" id="modal-confirm-button" class="btn btn-success">Confirm</button>
			</div>
		</div>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->addJS("js/members/delete.js");
$footer->render();
?>