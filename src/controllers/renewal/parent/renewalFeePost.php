<?

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
$partial_reg = false; //isPartialRegistration();

$partial_reg_require_topup = false;
/*if ($partial_reg) {
	global $db;
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ? AND RR = 0 AND ClubPays = 0";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['UserID']]);
	} catch (PDOException $e) {
		halt(500);
	}
	if ($query->fetchColumn() == 1) {
		$partial_reg_require_topup = true;
	}
}*/

$sql = "SELECT * FROM `members` WHERE `members`.`UserID` = '$user' AND
`ClubPays` = '0';";
$result = mysqli_query($link, $sql);

$clubFee = 0;
$totalFee = 0;

$payingSwimmerCount = mysqli_num_rows($result);

if ($payingSwimmerCount == 1) {
	$clubFee = 4000;
} else if ($partial_reg_require_topup) {
	$clubFee = 1000;
} else if ($payingSwimmerCount > 1 && !$partial_reg) {
	$clubFee = 5000;
} else {
	$clubFee = 0;
}

if ($partial_reg) {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = '$user' && `members`.`RR` = 1;";
} else {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = '$user';";
}
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);

$totalFee += $clubFee;

$asaFees = [];
$member = [];

for ($i = 0; $i < $count; $i++) {
	$member[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ClubPays']) {
		$asaFees[$i] = 1685;
	} else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = 3415;
	} else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = 1295;
	}
	$totalFee += $asaFees[$i];
}

$clubFeeString = number_format($clubFee/100,2,'.','');
$totalFeeString = number_format($totalFee/100,2,'.','');

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
$sql = "SELECT * FROM `paymentPreferredMandate` WHERE `UserID` = '$user';";
$hasDD = false;
if (mysqli_num_rows(mysqli_query($link, $sql)) == 1) {
	$hasDD = true;
}

if ($hasDD) {
	// INSERT Payment into pending
	$date = mysqli_real_escape_string($link, date("Y-m-d"));
	$description = "Membership Renewal";
	for ($i = 0; $i < $count; $i++) {
		$description .= ", " . $member[$i]['MForename'];
	}
	$sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`,
	`Amount`, `Currency`, `Type`) VALUES ('$date', 'Pending', '$user',
	'$description', '$totalFee', 'GBP', 'Payment');";
	mysqli_query($link, $sql);

	// Add swimmers to renewal list
	$sql = "SELECT `PaymentID` FROM `paymentsPending` WHERE `UserID` = '$user' AND
	`Status` = 'Pending' AND `Amount` = '$totalFee' AND `Name` = '$description';";
	$payID = mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC)['PaymentID'];
	$renewal = mysqli_real_escape_string($link, $renewal);
	for ($i = 0; $i < $count; $i++) {
		$memID = $member[$i]['MemberID'];
		$date = mysqli_real_escape_string($link, date("Y-m-d H:i:s"));
		$sql = "INSERT INTO `renewalMembers` (`PaymentID`, `MemberID`, `RenewalID`, `Date`)
		VALUES ('$payID', '$memID', '$renewal', '$date');";
		mysqli_query($link, $sql);
	}

	// Update the database with current renewal state
	$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE
	`RenewalID` = '$renewal' AND `UserID` = '$user';";
	mysqli_query($link, $sql);

	global $db;

	if (user_needs_registration($_SESSION['UserID'])) {
		$sql = "UPDATE `users` SET `RR` = 0 WHERE `UserID` = ?";
		try {
			$query = $db->prepare($sql);
			$query->execute([$_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
		header("Location: " . autoUrl(""));
	} else {
		header("Location: " . app('request')->curl);
	}
} else {
	header("Location: " . autoUrl("renewal/payments/setup"));
}
