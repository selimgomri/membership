<?

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM `members` LEFT JOIN `memberMedical` ON members.MemberID =
memberMedical.MemberID WHERE members.MemberID = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(500);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (($_SESSION['AccessLevel'] == "Parent" || $_SESSION['AccessLevel'] == "Galas" ||
$_SESSION['AccessLevel'] == "Coach") && $row['UserID'] != $_SESSION['UserID']) {
	halt(404);
}

setupMedicalInfo($id);

$conditions = $allergies = $medicine = "";

if ($_POST['medConDis'] == 1) {
	$conditions = mysqli_real_escape_string($link, ucfirst($_POST['medConDisDetails']));
}

if ($_POST['allergies'] == 1) {
	$allergies = mysqli_real_escape_string($link, ucfirst($_POST['allergiesDetails']));
}

if ($_POST['medicine'] == 1) {
	$medicine = mysqli_real_escape_string($link, ucfirst($_POST['medicineDetails']));
}

$sql = "UPDATE `memberMedical` SET `Conditions` = '$conditions', `Allergies` =
'$allergies', `Medication` = '$medicine' WHERE `MemberID` = '$id';";
if (mysqli_query($link, $sql)) {
	// Update the database with current renewal state

	$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
	$sql = "SELECT * FROM `members` WHERE `UserID` = '$user' AND `MemberID` > '$id' ORDER BY `MemberID` ASC
	LIMIT 1;";
	$result = mysqli_query($link, $sql);

	$renewal = mysqli_real_escape_string($link, $renewal);
	if (mysqli_num_rows($result) == 0) {
		$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = '0',
		`Part` = '0' WHERE `RenewalID` = '$renewal' AND `UserID` = '$user';";
		mysqli_query($link, $sql);
	} else {
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$member = mysqli_real_escape_string($link, $row['MemberID']);
		$sql = "UPDATE `renewalProgress` SET `Part` = '$member' WHERE `RenewalID` =
		'$renewal' AND `UserID` = '$user';";
		mysqli_query($link, $sql);
	}
	header("Location: " . app('request')->curl);
} else {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>An error occured when we tried to update our records</strong>
	<p class=\"mb-0\">Please try again. Your membership renewal will not be
	affected by this error.</p></div>";
	header("Location: " . app('request')->curl);
}
