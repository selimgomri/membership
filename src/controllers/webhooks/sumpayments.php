<?php

ignore_user_abort(true);
set_time_limit(0);

$sql = "SELECT * FROM `paymentMonths` ORDER BY `Date` DESC LIMIT 1;";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_array($result);
  if ($row['MonthStart'] != date("Y-m")) {
    $ms = date("Y-m");
    $date = $ms . "-01";
    $sql = "INSERT INTO `paymentMonths` (`MonthStart`, `Date`) VALUES ('$ms', '$date');";
    mysqli_query($link, $sql);
  }
}

$date = date("Y-m") . "-01";

// If pending payments for last month, get them and sum them for each user
$sql = "SELECT DISTINCT `UserID` FROM `paymentsPending` WHERE `Status` = 'Pending' AND `Date` < '$date' AND `Type` = 'Payment';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
for ($i = 0; $i < $count; $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $sumUID = $row['UserID'];
  $sql = "SELECT SUM(`Amount`) FROM `paymentsPending` WHERE `UserID` = '$sumUID' AND `Status` = 'Pending' AND `Date` < '$date' AND `Type` = 'Payment';";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $amount = $row["SUM(`Amount`)"];
  $dateString = date("F Y", strtotime("first day of previous month")) . " DD";
  $sql = "INSERT INTO `payments` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'pending_api_request', '$sumUID', '$dateString', '$amount', 'GBP', 'Payment');";
	mysqli_query($link, $sql);

	$sql = "UPDATE `paymentsPending` SET `Status` = 'Queued' WHERE `UserID` = '$sumUID' AND `Status` = 'Pending' AND `Date` < '$date' AND `Type` = 'Payment';";
	mysqli_query($link, $sql);
}
