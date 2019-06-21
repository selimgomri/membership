<?php

global $db;

$disabled = "";

$date = date("Y-m-d");
$insertPayment = $db->prepare("INSERT INTO paymentsPending (`Date`, `Status`, UserID, `Name`, Amount, Currency, PMkey, `Type`, MetadataJSON) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$markAsCharged = $db->prepare("UPDATE galaEntries SET Charged = ?, FeeToPay = ? WHERE EntryID = ?");
$notify = $db->prepare("INSERT INTO notify (UserID, `Status`, `Subject`, `Message`, EmailType) VALUES (?, ?, ?, ?, ?)");

$getGala = $db->prepare("SELECT GalaName `name`, GalaFee fee, GalaVenue venue, GalaFeeConstant fixed FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
	halt(404);
}

$getEntries = $db->prepare("SELECT 50Free, 100Free, 200Free, 400Free, 800Free, 1500Free, 50Back, 100Back, 200Back, 50Breast, 100Breast, 200Breast, 50Fly, 100Fly, 200Fly, 100IM, 150IM, 200IM, 400IM, MForename, MSurname, EntryID, Charged, FeeToPay, MandateID, userOptions.Value OptOut, members.UserID FROM (((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) LEFT JOIN users ON members.UserID = users.UserID) LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) LEFT JOIN userOptions ON users.UserID = userOptions.User) WHERE galaEntries.GalaID = ? AND (userOptions.Option = 'GalaDirectDebitOptOut' OR userOptions.Option IS NULL) AND Charged = ? AND EntryProcessed = ? AND MandateID IS NOT NULL ORDER BY MForename ASC, MSurname ASC");
$getEntries->execute([$id, '0', '1']);

$swimsArray = [
  '50Free' => '50 Free',
  '100Free' => '100 Free',
  '200Free' => '200 Free',
  '400Free' => '400 Free',
  '800Free' => '800 Free',
  '1500Free' => '1500 Free',
  '50Back' => '50 Back',
  '100Back' => '100 Back',
  '200Back' => '200 Back',
  '50Breast' => '50 Breast',
  '100Breast' => '100 Breast',
  '200Breast' => '200 Breast',
  '50Fly' => '50 Fly',
  '100Fly' => '100 Fly',
  '200Fly' => '200 Fly',
  '100IM' => '100 IM',
  '150IM' => '150 IM',
  '200IM' => '200 IM',
  '400IM' => '400 IM'
];

while ($entry = $getEntries->fetch(PDO::FETCH_ASSOC)) {
	$amount = (int) ($_POST[$entry['EntryID'] . '-amount']*100);
	$hasNoDD = ($entry['MandateID'] == null) || ($entry['OptOut']);

	if ($amount > 0 && $amount <= 15000 && !$hasNoDD) {
		$count = 0;

		$swimsList = '<ul>';
		foreach($swimsArray as $colTitle => $text) {
			if ($entry[$colTitle]) {
				$swimsList .= '<li>' . $text . '</li>';
			}
		}
		$swimsList .= '</ul>';

		try {
			$db->beginTransaction();

			$amountString = number_format($_POST[$entry['EntryID'] . '-amount'], 2);

			$name = $entry['MForename'] . ' ' . $entry['MSurname'] . '\'s Gala Entry into ' . $gala['name'] .  ' (Entry #' . $entry['EntryID'] . ')';

			$jsonArray = [
				"Name" => $name
			];
			$json = json_encode($jsonArray);

			$insertPayment->execute([
				$date,
				'Pending',
				$entry['UserID'],
				'Gala Entry (#' . $entry['EntryID'] . ')',
				$amount,
				'GBP',
				null,
				'Payment',
				$json
			]);

			$markAsCharged->execute([
				true,
				$amountString,
				$entry['EntryID']
			]);

			$message = '<p>We\'ve added a charge to your bill for ' . htmlspecialchars($entry['MForename']) .  '\'s entry into ' . htmlspecialchars($gala['name']) . '. You\'ll be charged for this as part of your next direct debit payment to ' . htmlspecialchars(env('CLUB_NAME')) . '.</p>';

			$message .= '<p>This charge is to the value of <strong>&pound;' . $amountString . '</strong>. You will be able to see this charge in your pending charges and from the first day of next month, on your bill statement.</p>';

			$message .= '<p>For this gala you entered;</p>';
			$message .= $swimsList;

			$message .= '<p>Kind Regards<br> The ' . htmlspecialchars(env('CLUB_NAME')) . ' Team</p>';

			$notify->execute([
				$entry['UserID'],
				'Queued',
				'Payments: ' . $entry['MForename'] .  '\'s ' . $gala['name'] . ' Entry',
				$message,
				'Galas'
			]);

			$db->commit();
		} catch (Exception $e) {
			// A problem occured
			$db->rollBack();
			$_SESSION['ChargeUsersFailure'] = true;
		}
	} else if ($amount > 15000) {
		$_SESSION['OverhighChargeAmount'][$entry['EntryID']] = true;
	}
}

if (!isset($_SESSION['ChargeUsersFailure'])) {
	$_SESSION['ChargeUsersSuccess'] = true;
}
header("Location: " . currentUrl());