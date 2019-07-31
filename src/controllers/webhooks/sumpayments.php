<?php

ignore_user_abort(true);
set_time_limit(0);

global $db;
global $link;
global $systemInfo;

$squadFeeMonths = json_decode($systemInfo->getSystemOption('SquadFeeMonths'), true);
$squadFeeRequired = !bool($squadFeeMonths[date("m")]);

// Prepare things
$getSquadMetadata = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname, squads.SquadName, squads.SquadID, squads.SquadFee FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID = ? ORDER BY members.MForename ASC, members.MSurname ASC;");

$getExtraMetadata = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname, extras.ExtraName, extras.ExtraFee FROM ((members INNER JOIN `extrasRelations` ON members.MemberID = extrasRelations.MemberID) INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) WHERE members.UserID = ? ORDER BY members.MForename ASC, members.MSurname ASC;");

$track = $db->prepare("INSERT INTO `individualFeeTrack` (`MonthID`, `MemberID`, `UserID`, `Description`, `Amount`, `Type`) VALUES (?, ?, ?, ?, ?, ?)");

$addToPaymentsPending = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MetadataJSON`) VALUES (?, 'Pending', ?, ?, ?, 'GBP', 'Payment', ?);");

$updateIndivFeeTrack = $db->prepare("UPDATE `individualFeeTrack` SET `PaymentID` = ? WHERE ID = ?;");

$getMonthSum = $db->prepare("SELECT SUM(`Amount`) FROM `paymentsPending` WHERE `UserID` = ? AND `Status` = 'Pending' AND `Date` <= ? AND `Type` = ?;");

$addPaymentForCharge = $db->prepare("INSERT INTO `payments` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MandateID`, `PMkey`) VALUES (?, 'pending_api_request', ?, ?, ?, 'GBP', 'Payment', ?, ?);");

$setPaymentsPending = $db->prepare("UPDATE `paymentsPending` SET `Status` = 'Queued' WHERE `UserID` = ? AND `Status` = 'Pending' AND `Date` <= ? AND (`Type` = 'Payment' OR `Type` = 'Voucher');");

// Begin transaction
$db->beginTransaction();

try {

  $ms = date("Y-m");
  $date = date("Y-m") . "-01";

  $sql = $db->query("SELECT * FROM `paymentMonths` ORDER BY `Date` DESC LIMIT 1;");
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
    if ($row['MonthStart'] != $ms) {
      $sql = $db->prepare("INSERT INTO `paymentMonths` (`MonthStart`, `Date`) VALUES (?, ?);");
      $sql->execute([$ms, $date]);
    }
  } else {
    $sql = $db->prepare("INSERT INTO `paymentMonths` (`MonthStart`, `Date`) VALUES (?, ?);");
    $sql->execute([$ms, $date]);
  }

  $sql = $db->prepare("SELECT COUNT(*) FROM `paymentSquadFees` INNER JOIN `paymentMonths` ON paymentSquadFees.MonthID = paymentMonths.MonthID WHERE `MonthStart` = ? ORDER BY `Date` DESC LIMIT 1;");
  $sql->execute([$ms]);
  if ($sql->fetchColumn() == 0) {
    $sql = $db->prepare("SELECT `MonthID` FROM `paymentMonths` WHERE `MonthStart` = ? ORDER BY `Date` DESC LIMIT 1;");
    $sql->execute([$ms]);
    $mid = $sql->fetchColumn();
    if ($mid == null) {
      throw new Exception("Month ID is NULL");
    }
    $sql = $db->prepare("INSERT INTO `paymentSquadFees` (`MonthID`) VALUES (?);");
    $sql->execute([
      $mid
    ]);

    $sql = $db->query("SELECT `UserID` FROM `users` WHERE `AccessLevel` = 'Parent';");
    while ($user = $sql->fetchColumn()) {
      if ($squadFeeRequired) {
        $amount = monthlyFeeCost($link, $user, "int");
        if ($amount > 0) {
          $description = "Squad Fees";

          // Put together JSON Metadata
          $getSquadMetadata->execute([$user]);

          $members = [];
          $trackIds = [];

          while ($swimmerRow = $getSquadMetadata->fetch(PDO::FETCH_ASSOC)) {
            $member = [
              "Member"      => $swimmerRow['MemberID'],
              "MemberName"  => $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'],
              "FeeName"     => $swimmerRow['SquadName'],
              "Fee"         => $swimmerRow['SquadFee']
            ];
            $members[] = $member;

            $name = $description . " (" . $swimmerRow['SquadName'] . ")";

            if ($swimmerRow['SquadFee'] > 0) {
              $track_info = [
                $mid,
                $swimmerRow['MemberID'],
                $user,
                $name,
                floor($swimmerRow['SquadFee']*100),
                'SquadFee'
              ];

              $track->execute($track_info);

              $trackIds[] = $db->lastInsertId();

              // Add squad fee payment to payments
            }

          }

          $metadata = [
            "PaymentType"         => "SquadFees",
            "Members"             => $members
          ];

          $memID = $swimmerRow['MemberID'];
          $fee = (int) $swimmerRow['SquadFee']*100;

          $metadata = json_encode($metadata);

          $addToPaymentsPending->execute([
            $date,
            $user,
            $description,
            $amount,
            $metadata
          ]);

          $paymentID = $db->lastInsertId();
          
          foreach ($trackIds as $trackId) {
            $updateIndivFeeTrack->execute([
              $paymentID,
              $trackId
            ]);
          }
        }
      }

      $amount = monthlyExtraCost($link, $user, "int");
      if ($amount > 0) {
        $description = "Extra Fees";

        // Put together JSON Metadata
        $getExtraMetadata->execute([$user]);

        $members = [];
        $trackIds = [];

        while ($swimmerRow = $getExtraMetadata->fetch(PDO::FETCH_ASSOC)) {
          $member = [
            "Member"      => $swimmerRow['MemberID'],
            "MemberName"  => $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'],
            "FeeName"     => $swimmerRow['ExtraName'],
            "Fee"         => $swimmerRow['ExtraFee'],
            "FeeInt"      => floor($swimmerRow['ExtraFee']*100)
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
              floor($swimmerRow['ExtraFee']*100),
              'ExtraFee'
            ];

            $track->execute($track_info);

            $trackIds[] = $db->lastInsertId();
          }
        }

        $metadata = [
          "PaymentType"         => "ExtraFees",
          "Members"             => $members
        ];

        $metadata = json_encode($metadata);

        $addToPaymentsPending->execute([
          $date,
          $user,
          $description,
          $amount,
          $metadata
        ]);

        // Get Payment ID
        $paymentID = $db->lastInsertId();

        foreach ($trackIds as $trackId) {
          $updateIndivFeeTrack->execute([
            $paymentID,
            $trackId
          ]);
        }
      }

      // Begin to sum up payments
      $getMonthSum->execute([$user, $date, 'Payment']);
      $amount = $getMonthSum->fetchColumn();

      $getMonthSum->execute([$user, $date, 'Refund']);
      $userDiscount = $getMonthSum->fetchColumn();

      $amount = $amount - $userDiscount;

      $dateString = date("F Y", strtotime("first day of this month")) . " DD";
      if ($amount > 100) {
        $addPaymentForCharge->execute([
          $date,
          $user,
          $dateString,
          $amount,
          null,
          null
        ]);
        $setPaymentsPending->execute([
          $user,
          $date
        ]);
      }
    }
    
    // Add Swimmers with No Parent to Fee Tracker
    if ($squadFeeRequired) {
      // // Squad Fees
      $sql = $db->query("SELECT `members`.`MemberID`, `SquadFee`, `SquadName` FROM `members` INNER JOIN `squads` ON squads.SquadID = members.SquadID WHERE `members`.`UserID` IS NULL AND `ClubPays` = 0");

      $add_track = $db->prepare("INSERT INTO `individualFeeTrack` (`MonthID`, `PaymentID`, `MemberID`, `UserID`, `Amount`, `Description`, `Type`, `NC`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

      while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        // Add to tracker
        if ($row['SquadFee'] > 0) {
          $add_track->execute([
            $mid,
            null,
            $row['MemberID'],
            null,
            floor($row['SquadFee']*100),
            "Squad Fees (" . $row['SquadName'] . ")",
            'SquadFee',
            true
          ]);
        }
      }
    }

    $sql = $db->query("SELECT `members`.`MemberID`, `ExtraFee`, `ExtraName` FROM ((`members` INNER JOIN `extrasRelations` ON extrasRelations.MemberID = members.MemberID) INNER JOIN `extras` ON extrasRelations.ExtraID = extras.ExtraID) WHERE `members`.`UserID` IS NULL");

    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
      // Add to tracker
      if ($row['ExtraFee'] > 0) {
        $add_track->execute([
          $mid,
          null,
          $row['MemberID'],
          null,
          floor($row['ExtraFee']*100),
          "Extra Fees (" . $row['ExtraName'] . ")",
          'ExtraFee',
          true
        ]);
      }
    }
  }

  // Commit operations
  $db->commit();
  echo "Operation Successful";
} catch (Exception $e) {
  $db->rollBack();
  reportError($e);
}