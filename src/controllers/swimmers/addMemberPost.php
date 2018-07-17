<?

$added = $action = false;

$forename = $middlenames = $surname = $dateOfBirth = $asaNumber = $sex = $squad = $cat = $cp = $sql = "";
$getASA = false;

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
	} else {
		$getASA = true;
	}
	if ($asaNumber == "") {
		$getASA = true;
	}
	$cat = mysqli_real_escape_string($link, $_POST['cat']);
	if ($cat != 1 && $cat != 2 && $cat != 3) {
		halt(500);
	}
	if ($_POST['clubpays'] == 1) {
		$cp = 1;
	} else {
		$cp = 0;
	}

	$accessKey = generateRandomString(6);

	$sql = "INSERT INTO `members` (`MemberID`, `MForename`, `MMiddleNames`, `MSurname`, `DateOfBirth`, `ASANumber`, `Gender`, `SquadID`, `AccessKey`, `ASACategory`, `ClubPays`) VALUES (NULL, '$forename', '$middlenames', '$surname', '$dateOfBirth', '$asaNumber', '$sex', '$squad', '$accessKey', '$cat', '$cp');";
	$action = mysqli_query($link, $sql);

	$last_id = mysqli_insert_id($link);

	if ($getASA) {
		$asa = mysqli_real_escape_string($link, "CLSX" . $last_id);
		$sql = "UPDATE `members` SET `ASANumber` = '$asa' WHERE `MemberID` = '$last_id';";
		mysqli_query($link, $sql);
	}
}

if ($action) {
	header("Location: " . autoUrl("swimmers/parenthelp/" . $last_id));
} else {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
		<p class="mb-0">
			<strong>We were not able to add the new swimmer</strong>
			Please try again
		</p>
	</div>';
	header("Location: " . app('request')->curl);
}
