<?

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

$status = true;
$statusMessage = "";

$forename = $surname = $email = $emailComms = $sms = $smsComms = "";

if ($_POST['forename'] != "") {
	$forename = mysqli_real_escape_string($link, $_POST['forename']);
} else {
	$status = false;
	$statusMessage .= "<li>No Forename Supplied</li>";
}

if ($_POST['surname'] != "") {
	$surname = mysqli_real_escape_string($link, $_POST['surname']);
} else {
	$status = false;
	$statusMessage .= "<li>No Surname Supplied</li>";
}

if ($_POST['email'] != "") {
	$email = mysqli_real_escape_string($link, $_POST['email']);
} else {
	$status = false;
	$statusMessage .= "<li>No Email Address Provided</li>";
}

if (isset($_POST['emailContactOK']) && $_POST['emailContactOK'] == 1) {
	$emailComms = 1;
} else {
	$emailComms = 0;
}

if ($_POST['mobile'] != "") {
	$sms = mysqli_real_escape_string($link, $_POST['mobile']);
} else {
	$status = false;
	$statusMessage .= "<li>No Phone Number Provided</li>";
}

if (isset($_POST['smsContactOK']) && $_POST['smsContactOK'] == 1) {
	$smsComms = 1;
} else {
	$smsComms = 0;
}

if ($status) {
	$sql = "UPDATE `users` SET `Forename` = '$forename', `Surname` = '$surname',
	`EmailAddress` = '$email', `EmailComms` = '$emailComms', `Mobile` = '$sms',
	`MobileComms` = '$smsComms' WHERE `UserID` = '$user';";
	if (mysqli_query($link, $sql)) {
		// Success move on
		$renewal = mysqli_real_escape_string($link, $renewal);
		$sql = "UPDATE `renewalProgress` SET `Substage` = `Substage` + 1 WHERE `RenewalID` = '$renewal' AND `UserID` = '$user';";
		mysqli_query($link, $sql);
		header("Location: " . app('request')->curl);
	} else {
		$status = false;
		$statusMessage .= "<li>Database Error - Contact support</li>";
	}
}

if (!$status) {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>An error occured when we tried to update our records</strong>
	<ul class=\"mb-0\">" . $statusMessage . "</ul></div>";
	header("Location: " . app('request')->curl);
}
