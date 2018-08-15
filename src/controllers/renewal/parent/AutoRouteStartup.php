<?

// Welcome to the Parent Renewal Autorouter
// The script takes the user's saved state and continues where left off
// Also controls single session progress.

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

function renewalProgress($user) {
	global $link;
	$sql;
	$user = mysqli_real_escape_string($link, $user);
	if (user_needs_registration($user)) {
		$sql = "SELECT * FROM `renewalProgress` WHERE `RenewalID` = 0 AND `UserID` =
		'$user';";
	} else {
		$sql = "SELECT * FROM `renewals` LEFT JOIN `renewalProgress` ON renewals.ID =
		renewalProgress.RenewalID WHERE `StartDate` <= CURDATE() <= `EndDate` AND
		`UserID` = '$user' ORDER BY renewals.ID DESC, renewalProgress.ID DESC;";
	}
	return mysqli_query($link, $sql);
}

function latestRenewal() {
	global $link;
	$sql = "SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() <= `EndDate`
	ORDER BY renewals.ID DESC;";
	return mysqli_query($link, $sql);
}

$result = renewalProgress($user);

$renewal = null;

if (mysqli_num_rows($result) == 0) {
	// Create a new Progress Record
	$result = latestRenewal();
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$renewal = mysqli_real_escape_string($link, $row['ID']);
	if (user_needs_registration($user)) {
		$renewal = 0;
	}
	$date = mysqli_real_escape_string($link, date("Y-m-d"));
	$sql = "INSERT INTO `renewalProgress` (`UserID`, `RenewalID`, `Date`, `Stage`, `Substage`, `Part`) VALUES ('$user', '$renewal', '$date', '0', '0', '0');";
	mysqli_query($link, $sql);
} else {
	$result = latestRenewal();
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$renewal = mysqli_real_escape_string($link, $row['ID']);
	if (user_needs_registration($user)) {
		$renewal = 0;
	}
}

$result = renewalProgress($user);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$renewalName = $row['Name'];

$stage = $row['Stage'];
$substage = $row['Substage'];
$part = $row['Part'];

// End of startup code
