<?

global $db;
$member = null;

if (false/*isPartialRegistration()*/) {
	$member = getNextSwimmer($_SESSION['UserID'], 0, true);
} else {
	$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
	$sql = "SELECT * FROM `members` WHERE `UserID` = '$user' ORDER BY `MemberID` ASC
	LIMIT 1;";
	$result = mysqli_query($link, $sql);

	if (mysqli_num_rows($result) == 0) {
		halt(403);
	}

	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$member = mysqli_real_escape_string($link, $row['MemberID']);
}

$renewal = mysqli_real_escape_string($link, $renewal);

$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = '0',
`Part` = '$member' WHERE `RenewalID` = '$renewal' AND `UserID` = '$user';";

if (mysqli_query($link, $sql)) {
	header("Location: " . currentUrl());
} else {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<p class=\"mb-0\"><strong>An error occured when we tried to update our records</strong></p>
	<p class=\"mb-0\">Please try again</p>
	</div>";
	header("Location: " . currentUrl());
}
