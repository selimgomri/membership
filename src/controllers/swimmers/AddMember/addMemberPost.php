<?

global $db;

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

	if (isset($_SESSION['Swimmers-FamilyMode'])) {
		$sql = "INSERT INTO familyMembers (FamilyID, MemberID) VALUES (?, ?)";
		try {
			$db->prepare($sql)->execute([$_SESSION['Swimmers-FamilyMode']['FamilyId'], $last_id]);
		} catch (PDOException $e) {
			halt(500);
		}
	}

	if ($getASA) {
		$asa = mysqli_real_escape_string($link, "CLSX" . $last_id);
		$sql = "UPDATE `members` SET `ASANumber` = '$asa' WHERE `MemberID` = '$last_id';";
		mysqli_query($link, $sql);
	}

	try {
		$query = $db->prepare("SELECT `UserID` FROM `users` WHERE `AccessLevel` = ? AND `UserID` != ?");
		$query->execute(["Admin", $_SESSION['UserID']]);
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		halt(500);
	}

	try {
		$notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
		`ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 0, 'NewMember')";
		$query = $db->prepare($notify);
	} catch (PDOException $e) {
		halt(500);
	}
	$subject = "New Club Member";
	$message = '<p>' . htmlentities(getUserName($_SESSION['UserID'])) . ' has added a new member, ' . htmlentities($forename . ' ' . $surname) . ' to our online membership system.</p><p>We have sent you this email to ensure you\'re aware of this.</p>';
	for ($i = 0; $i < sizeof($result); $i++) {
		try {
			$query->execute([$result[$i]['UserID'], $subject, $message]);
		} catch (PDOException $e) {
			halt(500);
		}
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
