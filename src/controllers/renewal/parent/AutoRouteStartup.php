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
		renewalProgress.RenewalID WHERE `StartDate` <= CURDATE() AND CURDATE() <=
    `EndDate` AND `UserID` = '$user' ORDER BY renewals.ID DESC,
    renewalProgress.ID DESC;";
	}
	return mysqli_query($link, $sql);
}

function latestRenewal() {
	global $link;
	$sql = "SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() AND CURDATE() <= `EndDate`
	ORDER BY renewals.ID DESC;";
	return mysqli_query($link, $sql);
}

function getNextSwimmer($user, $current = 0, $rr_only = false) {
	global $db;
	$sql = "SELECT `MemberID` FROM `members` WHERE `UserID` = ? AND `MemberID` > ?";
	$data = [
		$user,
		$current
	];
	if ($rr_only == true) {
		$sql = "SELECT `MemberID` FROM `members` WHERE `UserID` = ? AND `MemberID` > ? AND `RR` = 1";
	}

	try {
		$query = $db->prepare($sql);
		$query->execute($data);
	} catch (PDOException $e) {
		halt(500);
	}
	$row = $query->fetch(PDO::FETCH_ASSOC);
	$member = $row['MemberID'];

	if (!$row) {
		return false;
	}
	return $member;
}

function isPartialRegistration() {
	global $db;
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['UserID']]);
	} catch (PDOException $e) {
		halt(500);
	}
	$total_swimmers = (int) $query->fetchColumn();
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ? AND RR = ? ORDER
	BY `MemberID` ASC";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['UserID'], 1]);
	} catch (PDOException $e) {
		halt(500);
	}
	$new_swimmers = (int) $query->fetchColumn();
	if ($total_swimmers != $new_swimmers) {
		return true;
	}
	return false;
}

$result = renewalProgress($user);

$renewal = null;

if (mysqli_num_rows($result) == 0) {
	// Create a new Progress Record
	$result = latestRenewal();
  if (mysqli_num_rows($result) == 0) {
    halt(404);
  }
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
