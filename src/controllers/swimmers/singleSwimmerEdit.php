<?php
$access = $_SESSION['AccessLevel'];
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
$catUpdate = $cpUpdate = false;
$update = false;
$successInformation = "";

$query = "SELECT * FROM members WHERE MemberID = '$id' ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$forename = $row['MForename'];
$middlename = $row['MMiddleNames'];
$surname = $row['MSurname'];
$asaNumber = $row['ASANumber'];
$dbUserID = "";
$dbUserID = $row['UserID'];
$squad = $row['SquadID'];
$dateOfBirth = $row['DateOfBirth'];
$sex = $row['Gender'];
$otherNotes = $row['OtherNotes'];
$dbAccessKey = $row['AccessKey'];
$cat = $row['ASACategory'];
$cp = $row['ClubPays'];

if (!empty($_POST['forename'])) {
	$newForename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
	if ($newForename != $forename) {
		$sql = "UPDATE `members` SET `MForename` = '$newForename' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$forenameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['middlenames'])) {
	$newMiddlenames = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['middlenames']))));
	if ($newMiddlenames != $middlename) {
		$sql = "UPDATE `members` SET `MMiddleNames` = '$newMiddlenames' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$middlenameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['surname'])) {
	$newSurname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
	if ($newSurname != $surname) {
		$sql = "UPDATE `members` SET `MSurname` = '$newSurname' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$surnameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['asa'])) {
	$newASANumber = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['asa']))));
	if ($newASANumber != $asaNumber) {
		$sql = "UPDATE `members` SET `ASANumber` = '$newASANumber' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$asaUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['userid'])) {
	$newUserID = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['userid']))));
	if ($newUserID != $dbUserID) {
		$sql = "UPDATE `members` SET `UserID` = '$newUserID' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$userUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['squad'])) {
	$newSquadID = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squad']))));
	if ($newSquadID != $squad) {
		$sql = "UPDATE `members` SET `SquadID` = '$newSquadID' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$squadUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['datebirth'])) {
	$newDateOfBirth = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['datebirth']))));
	// NEEDS WORK FOR DATE TO BE RIGHT
	if ($newDateOfBirth != $dateOfBirth) {
		$sql = "UPDATE `members` SET `DateOfBirth` = '$newDateOfBirth' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$dateOfBirthUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['sex'])) {
	$newSex = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['sex']))));
	if ($newSex != $sex) {
		$sql = "UPDATE `members` SET `Gender` = '$newSex' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$sexUpdate = true;
		$update = true;
	}
}
if (isset($_POST['cat'])) {
	$newCat = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['cat']))));
	if ($newCat != $cat && ($newCat == 1 || $newCat == 2 || $newCat == 3)) {
		$sql = "UPDATE `members` SET `ASACategory` = '$newCat' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$catUpdate = true;
		$update = true;
	}
}
if (isset($_POST['cp'])) {
	echo "Hello";
	$newCp = mysqli_real_escape_string($link, trim($_POST['cp']));
	if ($newCp != $cp && ($newCp == 0 || $newCp == 1)) {
		$sql = "UPDATE `members` SET `ClubPays` = '$newCp' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$cpUpdate = true;
		$update = true;
	}
}
if (isset($_POST['otherNotes'])) {
	$newOtherNotes = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['otherNotes'])));
	if ($newOtherNotes != $otherNotes) {
		$sql = "UPDATE `members` SET `OtherNotes` = '$newOtherNotes' WHERE `MemberID` = '$id'";
		mysqli_query($link, $sql);
		$otherNotesUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['swimmerDeleteDanger'])) {
	$deleteKey = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['swimmerDeleteDanger'])));
	if ($deleteKey == $dbAccessKey) {
		$sql = "DELETE FROM `members` WHERE `members`.`MemberID` = '$id'";
		mysqli_query($link, $sql);
		header("Location: " . autoUrl("swimmers"));
	}
}

$sqlSwim = "";
$sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames,
members.MSurname, members.ASANumber, members.ASACategory, members.ClubPays,
squads.SquadName, squads.SquadID, squads.SquadFee, squads.SquadCoach,
squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender,
members.OtherNotes , members.AccessKey FROM (members INNER JOIN squads ON
members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
$resultSwim = mysqli_query($link, $sqlSwim);
$rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
$pagetitle = "Swimmer: " . $rowSwim['MForename'] . " " . $rowSwim['MSurname'];
$title = null;
$content = '<form method="post"><div class="row align-items-center"><div class="col-sm-8"><h1>Editing ' . $rowSwim['MForename'] . ' ' . $rowSwim['MSurname'] . '</h1></div><div class="col-sm-4 text-right"><button type="submit" class="btn btn-success">Update</button> <a class="btn btn-dark" href="../' . $id . '">Exit Edit Mode</a></div></div><hr>';
$content .= "<div class=\"row\"><div class=\"col col-md-8\"><div class=\"mb-3 p-3 bg-white rounded box-shadow\">";
if ($update) {
$content .= '<div class="alert alert-success">
	<strong>We have updated</strong>
	<ul class="mb-0">';
		if ($forenameUpdate) { $content .= '<li>First name</li>'; }
		if ($middlenameUpdate) { $content .= '<li>Middle name(s)</li>'; }
		if ($surnameUpdate) { $content .= '<li>Last address</li>'; }
		if ($dateOfBirthUpdate) { $content .= '<li>Date of birth</li>'; }
		if ($asaUpdate) { $content .= '<li>ASA Number</li>'; }
		if ($userUpdate) { $content .= '<li>Parent</li>'; }
		if ($squadUpdate) { $content .= '<li>Squad</li>'; }
		if ($sexUpdate) { $content .= '<li>Sex</li>'; }
		if ($catUpdate) { $content .= '<li>ASA Category</li>'; }
		if ($cpUpdate) { $content .= '<li>Whether or not the club pays swimmer\'s
		fees</li>'; }
		if ($otherNotesUpdate) { $content .= '<li>Other notes</li>'; }
$content .= '
	</ul>
</div>';
}
$content .= "
<div class=\"form-group\">
	<label for=\"forename\">Forename</label>
	<input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" value=\"" . $rowSwim['MForename'] . "\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"middlenames\">Middle Names</label>
	<input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\" value=\"" . $rowSwim['MMiddleNames'] . "\">
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"surname\">Surname</label>
	<input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" value=\"" . $rowSwim['MSurname'] . "\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"datebirth\">Date of Birth</label>
	<input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" placeholder=\"YYYY-MM-DD\" value=\"" . $rowSwim['DateOfBirth'] . "\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"asa\">ASA Registration Number</label>
	<input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\" placeholder=\"ASA Registration Numer\" value=\"" . $rowSwim['ASANumber'] . "\">
</div>";
$cat = [];
$cat[$rowSwim['ASACategory']] = " selected ";
$content .= "
<div class=\"form-group\">
	<label for=\"cat\">ASA Membership Category</label>
	<select class=\"custom-select\" id=\"cat\" name=\"cat\" placeholder=\"Select\">
		<option value=\"1\" " . $cat[1] . ">Category 1</option>
		<option value=\"2\" " . $cat[2] . ">Category 2</option>
		<option value=\"3\" " . $cat[3] . ">Category 3</option>
	</select>
</div>";
/*$sql = "SELECT COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'chesterlestreetasc_co_uk_membership'
AND TABLE_NAME = 'members'
AND COLUMN_NAME = 'Gender';";
$resultGender = mysqli_query($link, $sqlSwim);*/
if ($rowSwim['Gender'] == "Male") {
	$content .= "
	<div class=\"form-group\">
		<label for=\"sex\">Sex</label>
		<select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
			<option value=\"Male\" selected>Male</option>
			<option value=\"Female\">Female</option>
		</select>
	</div>";
}
else {
	$content .= "
	<div class=\"form-group\">
		<label for=\"sex\">Sex</label>
		<select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
			<option value=\"Male\">Male</option>
			<option value=\"Female\" selected>Female</option>
		</select>
	</div>";
}
$sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
$result = mysqli_query($link, $sql);
$squadCount = mysqli_num_rows($result);
$content .= "
<div class=\"form-group\">
	<label for=\"squad\">Squad</label>
		<select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
//$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
for ($i = 0; $i < $squadCount; $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$content .= "<option value=\"" . $row['SquadID'] . "\"";
	if ($row['SquadID'] == $rowSwim['SquadID']) {
		$content .= " selected";
	}
	$content .= ">" . $row['SquadName'] . "</option>";
}
$content .= "</select></div>";

$cp = [];
$cp[$rowSwim['ClubPays']] = " selected ";
$content .= "
<div class=\"form-group\">
	<label for=\"cp\">Club Pays Fees?</label>
	<select class=\"custom-select\" id=\"cp\" name=\"cp\" placeholder=\"Select\">
		<option value=\"0\" " . $cp[0] . ">No</option>
		<option value=\"1\" " . $cp[1] . ">Yes</option>
	</select>
</div>";

$content .= '<div class="form-group"> <label>Medical Notes</label>';
$content .= '<a class="d-block" href="' . autoUrl("swimmers/" . $id . "/medical") . '"
target="_self">Edit medical notes</a>';
$content .= '</div>';
$content .= "
<div class=\"form-group\">
	<label for=\"otherNotes\">Other Notes</label>
	<textarea class=\"form-control\" id=\"otherNotes\" name=\"otherNotes\" rows=\"3\" placeholder=\"Tell us any other notes for coaches\">" . $rowSwim['OtherNotes'] . "</textarea>
</div>";
if ($access == "Admin" || $access == "Committee") {
$content .= "
	<div class=\"alert alert-danger\">
		<div class=\"form-group mb-0\">
			<label for=\"swimmerDeleteDanger\"><strong>Danger Zone</strong> <br>Delete this Swimmer with this Key \"<span class=\"mono\">" . $rowSwim['AccessKey'] . "</span>\"</label>
			<input type=\"text\" class=\"form-control\" id=\"swimmerDeleteDanger\" name=\"swimmerDeleteDanger\" aria-describedby=\"swimmerDeleteDangerHelp\" placeholder=\"Enter the key\" onselectstart=\"return false\" onpaste=\"return false;\" onCopy=\"return false\" onCut=\"return false\" onDrag=\"return false\" onDrop=\"return false\" autocomplete=off>
			<small id=\"swimmerDeleteDangerHelp\" class=\"form-text\">Enter the key in quotes above and press submit. This will delete this swimmer.</small>
		</div>
	</div>";
}
$content .= "<button type=\"submit\" class=\"btn btn-outline-dark\">Update</button>";
$content .= "</div></div><div class=\"col-md-4\">";
$content .= "<div class=\"mb-3 p-3 bg-white rounded box-shadow\"><h2>Squad Information</h2><ul class=\"mb-0\"><li>Squad: " . $rowSwim['SquadName'] . "</li><li>Monthly Fee: &pound;" . $rowSwim['SquadFee'] . "</li>";
if ($rowSwim['SquadTimetable'] != "") {
	$content .= "<li><a href=\"" . $rowSwim['SquadTimetable'] . "\">Squad Timetable</a></li>";
}
if ($rowSwim['SquadCoC'] != "") {
	$content .= "<li><a href=\"" . $rowSwim['SquadCoC'] . "\">Squad Code of Conduct</a></li>";
}
$content .= "</ul></div>";
$content .= "</div></div></form>";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
?>
