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
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

while ($entry = $getEntries->fetch(PDO::FETCH_ASSOC)) {
  $hasNoDD = ($entry['MandateID'] == null) || ($entry['OptOut']);
	$count = 0;
	foreach($swimsArray as $colTitle => $text) {

		if ($entry[$colTitle]) {
			//<li>$text</li>
		}
	}

	try {
		$db->beginTransaction();

		$amount = (int) ($_POST[$entry['EntryID'] . '-amount']*100);
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
			$amount,
			$entry['EntryID']
		]);

		$message = '<p>We\'ve added a charge to your bill for ' . htmlspecialchars($entry['MForename']) .  '\'s entry into ' . htmlspecialchars($gala['name']) . '. You\'ll be charged for this as part of your next direct debit payment to ' . htmlspecialchars(env('CLUB_NAME')) . '.</p>';

		$message .= '<p>This charge is to the value of <strong>&pound;' . $amountString . '</strong>. You will be able to see this charge in your pending charges and from the first day of next month, on your bill statement.</p>';
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
}

if (!isset($_SESSION['ChargeUsersFailure'])) {
	$_SESSION['ChargeUsersSuccess'] = true;
}
header("Location: " . currentUrl());