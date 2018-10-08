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

$info = null;
if ($type == "squads") {
	$name_type = "SquadFee";
	$info = "Squad";
	$title_string = "Squad Fee payments for " . $dateString;
} else if ($type == "extras") {
	$name_type = "ExtraFee";
	$title_string = "Extra Fee payments for " . $dateString;
} else {
	halt(404);
}

$title = "Squad Fees - " . $dateString;

$sql = "SELECT `users`.`UserID`, `Forename`, `Surname`, `MForename`, `MSurname`,
individualFeeTrack.Amount, individualFeeTrack.Description, payments.Status, payments.PaymentID FROM
(((((`individualFeeTrack` LEFT JOIN `paymentMonths` ON
individualFeeTrack.MonthID = paymentMonths.MonthID) LEFT JOIN `paymentsPending`
ON individualFeeTrack.PaymentID = paymentsPending.PaymentID) LEFT JOIN
`members` ON members.MemberID = individualFeeTrack.MemberID) LEFT JOIN
`payments` ON paymentsPending.PMkey = payments.PMkey) LEFT JOIN `users` ON
users.UserID = individualFeeTrack.UserID) WHERE `paymentMonths`.`Date` LIKE
'$searchDate' AND `individualFeeTrack`.`Type` = '$name_type' ORDER BY `Forename`
ASC, `Surname` ASC, `users`.`UserID` ASC, `MForename` ASC, `MSurname` ASC;";

$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$user_id = $row['UserID'];
$user_id_last = null;

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=MonthlyFeesExport' . $year . '-' . $month . '.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array($title));
fputcsv($output, array('Parent', 'Swimmer', $info, 'Amount', 'FamilyTotal', 'Paid'));

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$name = null;
	$member = null;
	$amount = null;
	$family_total = null;

	if ($row['Forename'] != null && $row['Surname'] != null) {
		if ($user_id_last != $user_id) {
			$name = $row['Forename'] . " " . $row['Surname'];
			$family_total = '£' . number_format(monthlyFeeCost($link, $user_id, "decimal"),2,'.','');
		}
	} else {
		$name = "N/A";
	}

	$member = $row['MForename'] . " " . $row['MSurname'];

	$amount = '£' . number_format(($row['Amount']/100),2,'.','');

	fputcsv($output, array($name, $member, $row['Description'], $amount, $family_total));

	if ($i < mysqli_num_rows($result)-1) {
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$user_id_last = $user_id;
		$user_id = $row['UserID'];
	}
}
