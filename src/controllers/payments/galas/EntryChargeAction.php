<?php

$entryID = mysqli_real_escape_string($link, $_POST['entryID']);
$amount = mysqli_real_escape_string($link, $_POST['pay']);
$refund = mysqli_real_escape_string($link, $_POST['refund']);

$sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = '$entryID' AND `Charged` = '0' LIMIT 1;";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) != 1) {
	halt(500);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$amount =					(int) (number_format($amount,2,'.','')*100);
$refund = 				(int) (number_format($refund,2,'.','')*100);
$charge = 				($amount - $refund);
$user =						$row['UserID'];
$description =		$row['MForename'] . " " . $row['MSurname'] . ", Gala Entry into " . $row['GalaName'];

if ($charge < $amount) {
	$description .= " (Partially Refunded)";
}

$date = 					date("Y-m-d");

$sql = "UPDATE `galaEntries` SET `Charged` = '1' WHERE `EntryID` = '$entryID';";
mysqli_query($link, $sql);

if ($charge > 0) {
	$sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'Pending', '$user', '$description', $charge, 'GBP', 'Payment');";
	mysqli_query($link, $sql);
}

header("Location: " . autoUrl("payments/galas/" . $id));
