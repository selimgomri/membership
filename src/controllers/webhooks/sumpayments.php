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
    $user = mysqli_real_escape_string($link, $row[$i]['UserID']);
    $amount = monthlyFeeCost($link, $user, "int");
    if ($amount > 0) {
      $description = "Squad Fees";

      // Put together JSON Metadata
      $sql = "SELECT members.MemberID, members.MForename, members.MSurname, squads.SquadName,
      squads.SquadID, squads.SquadFee FROM (members INNER JOIN squads ON
      members.SquadID = squads.SquadID) WHERE members.UserID = '$user' ORDER
      BY members.MForename ASC, members.MSurname ASC;";
      $swimmers = mysqli_query($link, $sql);
      $count = mysqli_num_rows($swimmers);

      $members = [];

      for ($y = 0; $y < $count; $y++) {
        $swimmerRow = mysqli_fetch_array($swimmers, MYSQLI_ASSOC);
        $member = [
          "Member"      => $swimmerRow['MemberID'],
          "MemberName"  => $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'],
          "FeeName"     => $swimmerRow['SquadName'],
          "Fee"         => $swimmerRow['SquadFee']
        ];
        $members[] = $member;

        $name = $description . " (" . $swimmerRow['SquadName'] . ")";

        if ($swimmerRow['SquadFee'] > 0) {
          global $db;
          $track_info = [
            $mid,
            $swimmerRow['MemberID'],
            $user,
            $name,
            floor($swimmerRow['SquadFee']*100)
          ];

          try {
            $tracker_sql = "INSERT INTO `individualFeeTrack` (`MonthID`, `MemberID`,
            `UserID`, `Description`, `Amount`, `Type`) VALUES (?, ?,
            ?, ?, ?, 'SquadFee')";
            $db->prepare($tracker_sql)->execute($track_info);
          } catch (Exception $e) {
            halt(500);
          }
        }
      }

      $metadata = [
        "PaymentType"         => "SquadFees",
        "Members"             => $members
      ];

      $memID = mysqli_real_escape_string($link, $swimmerRow['MemberID']);
      $fee = (int) (mysqli_real_escape_string($link, $swimmerRow['SquadFee'])*100);

      $metadata = mysqli_real_escape_string($link, json_encode($metadata));

      $sql = "";
      if (userHasMandates($user)) {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MetadataJSON`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Payment', '$metadata');";
      } else {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MetadataJSON`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Payment', '$metadata');";
      }
      mysqli_query($link, $sql);

      // Get Payment ID
      /*$sql = "SELECT `PaymentID` FROM `paymentsPending` WHERE `Date` = '$date'
      AND `UserID` = '$user' AND `Amount` = '$amount' AND `Name` =
      '$description';";
      $paymentID = mysqli_real_escape_string($link,mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC)['PaymentID']);*/

      $paymentID = mysqli_real_escape_string($link, mysqli_insert_id($link));

      $sql = "UPDATE `individualFeeTrack` SET `PaymentID` = '$paymentID' WHERE `UserID` = '$user' AND `PaymentID` IS NULL;";
      mysqli_query($link, $sql);
    }
  }
  for ($i = 0; $i < mysqli_num_rows($result); $i++) {
    $user = $row[$i]['UserID'];
    $amount = monthlyExtraCost($link, $user, "int");
    if ($amount > 0) {
      $description = "Extra Fees";

      // Put together JSON Metadata
      $sql = "SELECT members.MemberID, members.MForename, members.MSurname,
      extras.ExtraName, extras.ExtraFee FROM ((members INNER JOIN
      `extrasRelations` ON members.MemberID = extrasRelations.MemberID) INNER
      JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) WHERE
      members.UserID = '$user' ORDER BY members.MForename ASC,
      members.MSurname ASC;";
      $swimmers = mysqli_query($link, $sql);
      $count = mysqli_num_rows($swimmers);

      $members = [];

      for ($y = 0; $y < $count; $y++) {
        $swimmerRow = mysqli_fetch_array($swimmers, MYSQLI_ASSOC);
        $member = [
          "Member"      => $swimmerRow['MemberID'],
          "MemberName"  => $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'],
          "FeeName"     => $swimmerRow['ExtraName'],
          "Fee"         => $swimmerRow['ExtraFee']
        ];
        $members[] = $member;

        $name = $description . " (" . $swimmerRow['ExtraName'] . ")";

        if ($swimmerRow['ExtraFee'] > 0) {
          global $db;
          $track_info = [
            $mid,
            $swimmerRow['MemberID'],
            $user,
            $name,
            floor($swimmerRow['ExtraFee']*100)
          ];

          try {
            $tracker_sql = "INSERT INTO `individualFeeTrack` (`MonthID`, `MemberID`,
            `UserID`, `Description`, `Amount`, `Type`) VALUES (?, ?,
            ?, ?, ?, 'ExtraFee')";
            $db->prepare($tracker_sql)->execute($track_info);
          } catch (Exception $e) {
            halt(500);
          }
        }
      }

      $metadata = [
        "PaymentType"         => "ExtraFees",
        "Members"             => $members
      ];

      $metadata = mysqli_real_escape_string($link, json_encode($metadata));

      $memID = mysqli_real_escape_string($link, $swimmerRow['MemberID']);
      $fee = (int) (mysqli_real_escape_string($link, $swimmerRow['ExtraFee'])*100);

      $sql = "";
      if (userHasMandates($user)) {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MetadataJSON`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Payment', '$metadata');";
      } else {
        $sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MetadataJSON`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Payment', '$metadata');";
      }
      mysqli_query($link, $sql);

      // Get Payment ID
      $sql = "SELECT `PaymentID` FROM `paymentsPending` WHERE `Date` = '$date'
      AND `UserID` = '$user' AND `Amount` = '$amount' AND `Name` =
      '$description';";
      $paymentID = mysqli_real_escape_string($link,mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC)['PaymentID']);

      $sql = "UPDATE `individualFeeTrack` SET `PaymentID` = '$paymentID' WHERE `UserID` = '$user' AND `PaymentID` IS NULL AND `NC` = 0;";
      mysqli_query($link, $sql);
    }
  }
  $sql = "INSERT INTO `paymentSquadFees` (`MonthID`) VALUES ('$mid');";
  mysqli_query($link, $sql);

  // Add Swimmers with No Parent to Fee Tracker
  // // Squad Fees
  $sql = "SELECT `members`.`MemberID`, `SquadFee`, `SquadName` FROM `members` INNER JOIN `squads` ON squads.SquadID = members.SquadID WHERE `members`.`UserID` IS NULL AND `ClubPays` = 0";
  try {
    $query = $db->prepare($sql);
    $query->execute();
  } catch (Exception $e) {
    halt(500);
  }

  $row = $query->fetchAll(PDO::FETCH_ASSOC);

  $add_track = $db->prepare("INSERT INTO `individualFeeTrack` (`MonthID`, `PaymentID`, `MemberID`, `UserID`, `Amount`, `Description`, `Type`, `NC`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

  for ($i = 0; $i < sizeof($row); $i++) {
    // Add to tracker
    if ($row[$i]['SquadFee'] > 0) {
      $add_track->execute([
        $mid,
        null,
        $row[$i]['MemberID'],
        null,
        floor($row[$i]['SquadFee']*100),
        "Squad Fees (" . $row[$i]['SquadName'] . ")",
        'SquadFee',
        true
      ]);
    }
  }

  $sql = "SELECT `members`.`MemberID`, `ExtraFee`, `ExtraName` FROM ((`members` INNER JOIN `extrasRelations` ON extrasRelations.MemberID = members.MemberID) INNER JOIN `extras` ON extrasRelations.ExtraID = extras.ExtraID) WHERE `members`.`UserID` IS NULL";
  try {
    $query = $db->prepare($sql);
    $query->execute();
  } catch (Exception $e) {
    halt(500);
  }

  $row = $query->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < sizeof($row); $i++) {
    // Add to tracker
    if ($row[$i]['ExtraFee'] > 0) {
      $add_track->execute([
        $mid,
        null,
        $row[$i]['MemberID'],
        null,
        floor($row[$i]['ExtraFee']*100),
        "Extra Fees (" . $row[$i]['ExtraName'] . ")",
        'ExtraFee',
        true
      ]);
    }
  }
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

  $sql = "SELECT SUM(`Amount`) FROM `paymentsPending` WHERE `UserID` = '$sumUID' AND `Status` = 'Pending' AND `Date` <= '$date' AND `Type` = 'Voucher';";
  $userDiscount = mysqli_query($link, $sql);
  $userDiscount = mysqli_fetch_array($userFees, MYSQLI_ASSOC);

  $amount = $userRow["SUM(`Amount`)"] - $userDiscount["SUM(`Amount`)"];

  $dateString = date("F Y", strtotime("first day of this month")) . " DD";
  $sql = "INSERT INTO `payments` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'pending_api_request', '$sumUID', '$dateString', '$amount', 'GBP', 'Payment');";
  if ($amount > 0) {
    mysqli_query($link, $sql);
  }

	$sql = "UPDATE `paymentsPending` SET `Status` = 'Queued' WHERE `UserID` = '$sumUID' AND `Status` = 'Pending' AND `Date` <= '$date' AND (`Type` = 'Payment' OR `Type` = 'Voucher');";
	mysqli_query($link, $sql);
}
