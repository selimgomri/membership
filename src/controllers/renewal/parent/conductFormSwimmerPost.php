<?
$id = mysqli_real_escape_string($link, $id);
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
