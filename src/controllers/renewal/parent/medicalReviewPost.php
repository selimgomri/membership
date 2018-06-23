<?

$id = mysqli_real_escape_string($link, $id);

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
	header("Location: " . app('request')->curl);
} else {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>An error occured when we tried to update our records</strong>
	<p class=\"mb-0\">Please try again. Your membership renewal will not be
	affected by this error.</p></div>";
	header("Location: " . app('request')->curl);
}
