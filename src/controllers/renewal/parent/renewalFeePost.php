<?php

global $db;
global $systemInfo;

$user = $_SESSION['UserID'];
$partial_reg = false;//isPartialRegistration();

$partial_reg_require_topup = false;
if ($partial_reg) {
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
}

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ? AND `ClubPays` = '0'");
$sql->execute([$_SESSION['UserID']]);

$clubFee = 0;
$totalFee = 0;

$payingSwimmerCount = $sql->fetchColumn();

if ($payingSwimmerCount == 1) {
	$clubFee = $systemInfo->getSystemOption('ClubFeeIndividual');
} else if ($partial_reg_require_topup) {
	$clubFee = $systemInfo->getSystemOption('ClubFeeFamily') - $clubFee;
} else if ($payingSwimmerCount > 1 && !$partial_reg) {
	$clubFee = $systemInfo->getSystemOption('ClubFeeIndividual');
} else {
	$clubFee = 0;
}

if ($partial_reg) {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = ? && `members`.`RR` = 1";
} else {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = ?";
}
$getMembers = $db->prepare($sql);
$getMembers->execute([$_SESSION['UserID']]);

$member = $getMembers->fetchAll(PDO::FETCH_ASSOC);
$count = sizeof($member);

$totalFee += $clubFee;

$asaFees = [];

$asa1 = $systemInfo->getSystemOption('ASA-County-Fee-L1') + $systemInfo->getSystemOption('ASA-Regional-Fee-L1') + $systemInfo->getSystemOption('ASA-National-Fee-L1');
$asa2 = $systemInfo->getSystemOption('ASA-County-Fee-L2') + $systemInfo->getSystemOption('ASA-Regional-Fee-L2') + $systemInfo->getSystemOption('ASA-National-Fee-L2');
$asa3 = $systemInfo->getSystemOption('ASA-County-Fee-L3') + $systemInfo->getSystemOption('ASA-Regional-Fee-L3') + $systemInfo->getSystemOption('ASA-National-Fee-L3');

for ($i = 0; $i < $count; $i++) {
	if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa1;
	} else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa2;
	} else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa3;
	}
	$totalFee += $asaFees[$i];
}

$clubFeeString = number_format($clubFee/100,2,'.','');
$totalFeeString = number_format($totalFee/100,2,'.','');

$sql = $db->prepare("SELECT COUNT(*) FROM `paymentPreferredMandate` WHERE `UserID` = ?");
$sql->execute([$_SESSION['UserID']]);
$hasDD = false;
if ($sql->fetchColumn() == 1) {
	$hasDD = true;
}

if ($hasDD) {
	// INSERT Payment into pending
	$date = new \DateTime('now', new DateTimeZone('Europe/London'));
	$date->setTimezone(new DateTimeZone('UTC'));
	$description = "Membership Renewal";
	if ($renewal == 0) {
		$description = "Club Registration Fee";
	}
	for ($i = 0; $i < $count; $i++) {
		$description .= ", " . $member[$i]['MForename'];
	}
	$insert = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$insert->execute([
		$date->format('Y-m-d'),
		'Pending',
		$_SESSION['UserID'],
		$description,
		$totalFee,
		'GBP',
		'Payment'
	]);
	$payID = $db->lastInsertId();

	if ($renewal != 0) {
		$insert = $db->prepare("INSERT INTO `renewalMembers` (`PaymentID`, `MemberID`, `RenewalID`, `Date`, `CountRenewal`) VALUES ('$payID', '$memID', '$renewal', '$date', 1)");
		for ($i = 0; $i < $count; $i++) {
			$memID = $member[$i]['MemberID'];
			$insert->execute([
				$payID,
				$memID,
				$renewal,
				$date->format("Y-m-d H:i:s"),
				true
			]);
		}
	}

	$progress = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?");
	$progress->execute([
		$renewal,
		$_SESSION['UserID']
	]);

	if (user_needs_registration($_SESSION['UserID'])) {
		$sql = "UPDATE `users` SET `RR` = 0 WHERE `UserID` = ?";
		try {
			$query = $db->prepare($sql);
			$query->execute([$_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
		
		try {
			$query = $db->prepare("UPDATE `members` SET `RR` = 0 WHERE `UserID` = ?");
			$query->execute([$_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}

		// Remove from status tracker
		$delete = $db->prepare("DELETE FROM renewalProgress WHERE UserID = ? AND RenewalID = ?");
		$delete->execute([
			$_SESSION['UserID'],
			$renewal
		]);
		header("Location: " . autoUrl(""));
	} else {
		header("Location: " . autoUrl("renewal/go"));
	}
} else {
	header("Location: " . autoUrl("renewal/payments/setup"));
}
