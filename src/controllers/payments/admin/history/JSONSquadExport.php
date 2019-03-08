<?php

global $db;
global $link;

use Respect\Validation\Validator as v;

if (!v::intVal()->between(1, 12)->validate((int) $month) || !v::stringType()->length(2, 2)->validate($month)) {
	halt(404);
}

if (!v::intVal()->min(1970, true)->validate((int) $year) || !v::stringType()->length(4, null)->validate($year)) {
	halt(404);
}

$searchDate = mysqli_real_escape_string($link, $year . "-" . $month . "-") . "%";
$name_type = null;
$title_string = null;

$dateString = date("F Y", strtotime($year . "-" . $month));

if ($type == "squads") {
	$name_type = "SquadFee";
	$title_string = "Squad Fee payments for " . $dateString;
} else if ($type == "extras") {
	$name_type = "ExtraFee";
	$title_string = "Extra Fee payments for " . $dateString;
} else {
	halt(404);
}

$title = "Squad Fees - " . $dateString;

$sql = "SELECT `users`.`UserID`, `Forename`, `Surname`, `MForename`, `MSurname`,
individualFeeTrack.Amount, individualFeeTrack.Description, payments.Status,
payments.PaymentID, `ClubPays` FROM (((((`individualFeeTrack` LEFT JOIN
`paymentMonths` ON individualFeeTrack.MonthID = paymentMonths.MonthID) LEFT JOIN
`paymentsPending` ON individualFeeTrack.PaymentID = paymentsPending.PaymentID)
LEFT JOIN `members` ON members.MemberID = individualFeeTrack.MemberID) LEFT JOIN
`payments` ON paymentsPending.PMkey = payments.PMkey) LEFT JOIN `users` ON
users.UserID = individualFeeTrack.UserID) WHERE `paymentMonths`.`Date` LIKE
'$searchDate' AND `individualFeeTrack`.`Type` = '$name_type' ORDER BY `Forename`
ASC, `Surname` ASC, `users`.`UserID` ASC, `MForename` ASC, `MSurname` ASC;";

$query = $db->prepare($sql);
$query->execute([$name_type]);

$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$user_id = $row['UserID'];
$user_id_last = null;

$export_array = [
	'Type'		=>	$name_type,
	'Date'		=> [
		'Year'		=> $year,
		'Month'		=> $month
	],
	'Parents'	=> []
];

// output headers so that the file is downloaded rather than displayed
header('Content-Type: application/json; charset=UTF-8');
//header('Content-Disposition: attachment; filename=MonthlyFeesExport' . $year . '-' . $month . '.csv');

$parents = [];
$parent = [];
$swimmers = [];

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$name = null;
	$member = null;
	$amount = null;
	$family_total = null;

	if ($row['Forename'] != null && $row['Surname'] != null) {
		if ($user_id_last != $user_id) {
			$parent['Name'] = $row['Forename'] . " " . $row['Surname'];
			$parent['FamilyTotal'] = monthlyFeeCost($link, $user_id, "int");
			$parent['FamilyTotalString'] = '£' . number_format(monthlyFeeCost($link, $user_id, "decimal"),2,'.','');
			$parent['PaymentStatus'] = $row['Status'];
			$parent['PaymentStatusString'] = paymentStatusString($row['Status']);
		}
	} else {
		$parent['Name'] = null;
		$parent['FamilyTotal'] = (int) $row['Amount'];
		$parent['FamilyTotalString'] = '£' . number_format(($row['Amount']/100),2,'.','');
		$parent['PaymentStatusString'] = "No Parent or Direct Debit Available";
	}

	$swimmer = [
		'Name'				=> $row['MForename'] . " " . $row['MSurname'],
		'Description'	=> $row['Description'],
		'Amount'			=> (int) $row['Amount'],
		'AmountString'=> '£' . number_format(($row['Amount']/100),2,'.',''),
		'ClubPaysFees'=> $row['ClubPays']
	];

	if ($row['ClubPays']) {
		$swimmer['ClubPaysFees'] = true;
	} else {
		$swimmer['ClubPaysFees'] = false;
	}

	$swimmers[] = $swimmer;
	$swimmer = [];

	if ($i < mysqli_num_rows($result)-1) {
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$user_id_last = $user_id;
		$user_id = $row['UserID'];

		if ($user_id != $user_id_last || $user_id_last == null || $user_id_last == "") {
			$parent['Swimmers'] = $swimmers;
			$export_array['Parents'][] = $parent;
			$parent = [];
			$swimmers = [];
		}
	}
}

echo json_encode($export_array);
