<?
$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
$renewal = mysqli_real_escape_string($link, $renewal);

$sql = "UPDATE `renewalProgress` SET `Substage` = `Substage` + 1 WHERE
`RenewalID` = '$renewal' AND `UserID` = '$user';";

if (mysqli_query($link, $sql)) {
	header("Location: " . app('request')->curl);
} else {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<p class=\"mb-0\"><strong>An error occured when we tried to update our records</strong></p>
	<p class=\"mb-0\">Please try again</p>
	</div>";
	header("Location: " . app('request')->curl);
}
