<?php
// Committee or Admin can see and change all data
$forenameUpdate = false;
$middlenameUpdate = false;
$surnameUpdate = false;
$asaUpdate = false;
$userUpdate = false;
$squadUpdate = false;
$dateOfBirthUpdate = false;
$sexUpdate = false;
$medicalNotesUpdate = false;
$otherNotesUpdate = false;
$update = false;
$successInformation = "";

$query = "SELECT * FROM members WHERE MemberID = '$idLast' ";
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
$medicalNotes = $row['MedicalNotes'];
$otherNotes = $row['OtherNotes'];
$dbAccessKey = $row['AccessKey'];

if (!empty($_POST['forename'])) {
	$newForename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
	if ($newForename != $forename) {
		$sql = "UPDATE `members` SET `MForename` = '$newForename' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$forenameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['middlenames'])) {
	$newMiddlenames = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['middlenames']))));
	if ($newMiddlenames != $middlename) {
		$sql = "UPDATE `members` SET `MMiddleNames` = '$newMiddlenames' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$middlenameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['surname'])) {
	$newSurname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
	if ($newSurname != $surname) {
		$sql = "UPDATE `members` SET `MSurname` = '$newSurname' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$surnameUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['asa'])) {
	$newASANumber = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['asa']))));
	if ($newASANumber != $asaNumber) {
		$sql = "UPDATE `members` SET `ASANumber` = '$newASANumber' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$asaUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['userid'])) {
	$newUserID = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['userid']))));
	if ($newUserID != $dbUserID) {
		$sql = "UPDATE `members` SET `UserID` = '$newUserID' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$userUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['squad'])) {
	$newSquadID = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squad']))));
	if ($newSquadID != $squad) {
		$sql = "UPDATE `members` SET `SquadID` = '$newSquadID' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$squadUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['datebirth'])) {
	$newDateOfBirth = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['datebirth']))));
	// NEEDS WORK FOR DATE TO BE RIGHT
	if ($newDateOfBirth != $dateOfBirth) {
		$sql = "UPDATE `members` SET `DateOfBirth` = '$newDateOfBirth' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$dateOfBirthUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['sex'])) {
	$newSex = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['sex']))));
	if ($newSex != $sex) {
		$sql = "UPDATE `members` SET `Gender` = '$newSex' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$sexUpdate = true;
		$update = true;
	}
}
if (isset($_POST['medicalNotes'])) {
	$newMedicalNotes = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['medicalNotes'])));
	if ($newMedicalNotes != $medicalNotes) {
		$sql = "UPDATE `members` SET `MedicalNotes` = '$newMedicalNotes' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$medicalNotesUpdate = true;
		$update = true;
	}
}
if (isset($_POST['otherNotes'])) {
	$newOtherNotes = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['otherNotes'])));
	if ($newOtherNotes != $otherNotes) {
		$sql = "UPDATE `members` SET `OtherNotes` = '$newOtherNotes' WHERE `MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		$otherNotesUpdate = true;
		$update = true;
	}
}
if (!empty($_POST['swimmerDeleteDanger'])) {
	$deleteKey = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['swimmerDeleteDanger'])));
	if ($deleteKey == $dbAccessKey) {
		$sql = "DELETE FROM `members` WHERE `members`.`MemberID` = '$idLast'";
		mysqli_query($link, $sql);
		header("Location: " . autoUrl("swimmers"));
	}
}

$sqlSwim = "";
$sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames, members.MSurname, members.ASANumber, squads.SquadName, squads.SquadID, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes , members.AccessKey FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$idLast';";
$resultSwim = mysqli_query($link, $sqlSwim);
$rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
$pagetitle = "Swimmer: " . $rowSwim['MForename'] . " " . $rowSwim['MSurname'];
$title = null;
$content = '<div class="row align-items-center"><div class="col-sm-8"><h1>Editing ' . $rowSwim['MForename'] . ' ' . $rowSwim['MSurname'] . '</h1></div><div class="col-sm-4 text-right"><a class="btn btn-dark" href="../' . $idLast . '">Exit Edit Mode</a></div></div><hr>';
$content .= "<div class=\"row\"><div class=\"col col-md-8\">";
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
		if ($medicalNotesUpdate) { $content .= '<li>Medical notes</li>'; }
		if ($otherNotesUpdate) { $content .= '<li>Other notes</li>'; }
$content .= '
	</ul>
</div>';
}
// Main Info Content
$content .= "<form method=\"post\">";
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
$content .= "
<div class=\"form-group\">
	<label for=\"medicalNotes\">Medical Notes</label>
	<textarea class=\"form-control\" id=\"medicalNotes\" name=\"medicalNotes\" rows=\"3\" placeholder=\"Tell us about any medical issues\">" . $rowSwim['MedicalNotes'] . "</textarea>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"otherNotes\">Other Notes</label>
	<textarea class=\"form-control\" id=\"otherNotes\" name=\"otherNotes\" rows=\"3\" placeholder=\"Tell us any other notes for coaches\">" . $rowSwim['OtherNotes'] . "</textarea>
</div>";
if ($access == "Admin" || $access == "Committee") {
$content .= "
	<div class=\"alert alert-danger\">
		<div class=\"form-group mb-0\">
			<label for=\"swimmerDeleteDanger\"><strong>Danger Zone</strong> <br>Delete this Swimmer with this Key \"" . $rowSwim['AccessKey'] . "\"</label>
			<input type=\"text\" class=\"form-control\" id=\"swimmerDeleteDanger\" name=\"swimmerDeleteDanger\" aria-describedby=\"swimmerDeleteDangerHelp\" placeholder=\"Enter the key\" onselectstart=\"return false\" onpaste=\"return false;\" onCopy=\"return false\" onCut=\"return false\" onDrag=\"return false\" onDrop=\"return false\" autocomplete=off>
			<small id=\"swimmerDeleteDangerHelp\" class=\"form-text\">Enter the key in quotes above and press submit. This will delete this swimmer.</small>
		</div>
	</div>";
}
$content .= "<button type=\"submit\" class=\"btn btn-outline-dark mb-3\">Update</button>";
$content .= "</div><div class=\"col-md-4\">";
$content .= "<div class=\"cell\"><h2>Squad Information</h2><ul class=\"mb-0\"><li>Squad: " . $rowSwim['SquadName'] . "</li><li>Monthly Fee: &pound;" . $rowSwim['SquadFee'] . "</li>";
if ($rowSwim['SquadTimetable'] != "") {
	$content .= "<li><a href=\"" . $rowSwim['SquadTimetable'] . "\">Squad Timetable</a></li>";
}
if ($rowSwim['SquadCoC'] != "") {
	$content .= "<li><a href=\"" . $rowSwim['SquadCoC'] . "\">Squad Code of Conduct</a></li>";
}
$content .= "</ul></div>";
$content .= "</div></div>";
