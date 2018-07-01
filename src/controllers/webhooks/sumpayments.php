<?php

ignore_user_abort(true);
set_time_limit(0);

$ms = date("Y-m");
$date = date("Y-m") . "-01";

$sql = "SELECT * FROM `paymentMonths` ORDER BY `Date` DESC LIMIT 1;";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_array($result);
  if ($row['MonthStart'] != $ms) {
    $sql = "INSERT INTO `paymentMonths` (`MonthStart`, `Date`) VALUES ('$ms', '$date');";
    mysqli_query($link, $sql);
  }
}

$sql = "SELECT * FROM `paymentSquadFees` INNER JOIN `paymentMonths` ON paymentSquadFees.MonthID = paymentMonths.MonthID WHERE `MonthStart` = '$ms' ORDER BY `Date` DESC LIMIT 1;";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) == 0) {
  $sql = "SELECT `MonthID` FROM `paymentMonths` WHERE `MonthStart` = '$ms' ORDER BY `Date` DESC LIMIT 1;";
  $row = mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC);
  $mid = $row['MonthID'];

  $sql = "SELECT `UserID` FROM `users` WHERE `AccessLevel` = 'Parent';";
  $result = mysqli_query($link, $sql);
  for ($i = 0; $i < mysqli_num_rows($result); $i++) {
    $row[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $user = $row[$i]['UserID'];
    $amount = monthlyFeeCost($link, $user, "int");
    if ($amount > 0) {
      $description = "Squad Fees";
      $sql = "";
      if (userHasMandates($user)) {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Payment');";
      } else {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'Paid', '$user', '$description', $amount, 'GBP', 'Payment');";
      }
      mysqli_query($link, $sql);
    }
  }
  for ($i = 0; $i < mysqli_num_rows($result); $i++) {
    $user = $row[$i]['UserID'];
    $amount = monthlyExtraCost($link, $user, "int");
    if ($amount > 0) {
      $description = "Extra Fees";
      $sql = "";
      if (userHasMandates($user)) {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Payment');";
      } else {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'Paid', '$user', '$description', $amount, 'GBP', 'Payment');";
      }
      mysqli_query($link, $sql);
    }
  }
  $sql = "INSERT INTO `paymentSquadFees` (`MonthID`) VALUES ('$mid');";
  mysqli_query($link, $sql);

}

// If pending payments for last month, get them and sum them for each user
$sql = "SELECT DISTINCT `UserID` FROM `paymentsPending` WHERE `Status` = 'Pending' AND `Date` <= '$date' AND `Type` = 'Payment';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
for ($i = 0; $i < $count; $i++) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $sumUID = $row['UserID'];
  $sql = "SELECT SUM(`Amount`) FROM `paymentsPending` WHERE `UserID` = '$sumUID' AND `Status` = 'Pending' AND `Date` <= '$date' AND `Type` = 'Payment';";
  $userFees = mysqli_query($link, $sql);
  $userRow = mysqli_fetch_array($userFees, MYSQLI_ASSOC);
  $amount = $userRow["SUM(`Amount`)"];
  $dateString = date("F Y", strtotime("first day of this month")) . " DD";
  $sql = "INSERT INTO `payments` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'pending_api_request', '$sumUID', '$dateString', '$amount', 'GBP', 'Payment');";
  if ($amount > 0) {
    mysqli_query($link, $sql);
  }

	$sql = "UPDATE `paymentsPending` SET `Status` = 'Queued' WHERE `UserID` = '$sumUID' AND `Status` = 'Pending' AND `Date` <= '$date' AND `Type` = 'Payment';";
	mysqli_query($link, $sql);
}
