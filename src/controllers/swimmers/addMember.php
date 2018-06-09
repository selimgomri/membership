<?php
$pagetitle = "Add a member";
$title = "Add a member";
$content = "<p class=\"lead\">Add a member to the club system.</p>";
$added = false;

$forename = $middlenames = $surname = $dateOfBirth = $asaNumber = $sex = $squad = $sql = "";

if ((!empty($_POST['forename']))  && (!empty($_POST['surname'])) && (!empty($_POST['datebirth'])) && (!empty($_POST['sex'])) && (!empty($_POST['squad']))) {
	$forename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
	$surname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
	$dateOfBirth = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['datebirth'])));
	$sex = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['sex'])));
	$squad = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['squad'])));
	if ((!empty($_POST['middlenames']))) {
		$middlenames = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['middlenames']))));
	}
	if ((!empty($_POST['asa']))) {
		$asaNumber = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['asa'])));
	}

	$accessKey = generateRandomString(6);

	$sql = "INSERT INTO `members` (`MemberID`, `MForename`, `MMiddleNames`, `MSurname`, `DateOfBirth`, `ASANumber`, `Gender`, `SquadID`, `AccessKey`) VALUES (NULL, '$forename', '$middlenames', '$surname', '$dateOfBirth', '$asaNumber', '$sex', '$squad', '$accessKey');";
	$action = mysqli_query($link, $sql);
	if ($action) {
		$added = true;
	}
}

$content = "<div class=\"row\"><div class=\"col col-md-8\">";
if ($added) {
$content .= '<div class="alert alert-success">
	<strong>We added the member</strong>';
$content .= '</div>';
}
// Main Info Content
$content .= "<form method=\"post\">";
$content .= "
<div class=\"form-group\">
	<label for=\"forename\">Forename</label>
	<input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"middlenames\">Middle Names</label>
	<input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\">
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"surname\">Surname</label>
	<input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"datebirth\">Date of Birth</label>
	<input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" placeholder=\"YYYY-MM-DD\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"asa\">ASA Registration Number</label>
	<input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\" placeholder=\"ASA Registration Numer\">
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"sex\">Sex</label>
	<select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
		<option value=\"Male\">Male</option>
		<option value=\"Female\">Female</option>
	</select>
</div>";
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
	$content .= ">" . $row['SquadName'] . "</option>";
}
$content .= "</select></div>";
$content .= "<button type=\"submit\" class=\"btn btn-outline-dark mb-3\">Add Member</button>";
$content .= "</div><div class=\"col-md-4\">";
$content .= "</div></div>";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
