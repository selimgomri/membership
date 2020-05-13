<?php

ignore_user_abort(true);
set_time_limit(0);

use Brick\Math\BigDecimal;

$db = app()->db;



$squadFeeMonths = json_decode(app()->tenant->getKey('SquadFeeMonths'), true);
$date = new DateTime('now', new DateTimeZone('Europe/London'));
$squadFeeRequired = !bool($squadFeeMonths[$date->format("m")]);

// Prepare things
$getSquadMetadata = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname, members.ClubPays, squads.SquadName, squads.SquadID, squads.SquadFee FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID = ? ORDER BY squads.SquadFee DESC, members.MForename ASC, members.MSurname ASC;");

$getExtraMetadata = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname, extras.ExtraName, extras.ExtraID, extras.ExtraFee, extras.Type FROM ((members INNER JOIN `extrasRelations` ON members.MemberID = extrasRelations.MemberID) INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) WHERE members.UserID = ? ORDER BY members.MForename ASC, members.MSurname ASC;");

$track = $db->prepare("INSERT INTO `individualFeeTrack` (`MonthID`, `MemberID`, `UserID`, `Description`, `Amount`, `Type`, `PaymentID`) VALUES (?, ?, ?, ?, ?, ?, ?)");

$addToPaymentsPending = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MetadataJSON`) VALUES (?, 'Pending', ?, ?, ?, 'GBP', 'Payment', ?);");

$addCreditToPaymentsPending = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MetadataJSON`) VALUES (?, 'Pending', ?, ?, ?, 'GBP', 'Refund', ?);");

$updateIndivFeeTrack = $db->prepare("UPDATE `individualFeeTrack` SET `PaymentID` = ? WHERE ID = ?;");

$getMonthSum = $db->prepare("SELECT SUM(`Amount`) FROM `paymentsPending` WHERE `UserID` = ? AND `Status` = 'Pending' AND `Date` <= ? AND `Type` = ?;");

$addPaymentForCharge = $db->prepare("INSERT INTO `payments` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`, `MandateID`, `PMkey`) VALUES (?, ?, ?, ?, ?, 'GBP', 'Payment', ?, ?);");

$setPaymentsPending = $db->prepare("UPDATE `paymentsPending` SET Payment = ?, `Status` = ? WHERE `UserID` = ? AND `Status` = 'Pending' AND `Date` <= ?");

$setPaymentsPendingPM = $db->prepare("UPDATE `paymentsPending` SET `PMkey` = ?WHERE `UserID` = ? AND `Status` = 'Pending' AND `Date` <= ?");

$getCountPending = $db->prepare("SELECT COUNT(*) FROM `paymentsPending` WHERE `UserID` = ? AND `Status` = 'Pending';");

// Begin transaction
$db->beginTransaction();

try {

  $dateTime = new DateTime('first day of this month', new DateTimeZone('Europe/London'));
  $ms = $dateTime->format('Y-m');
  $date = $dateTime->format('Y-m-d');

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

    $sql = $db->query("SELECT `UserID` FROM `users` INNER JOIN `permissions` ON users.UserID = `permissions`.`User` WHERE `Permission` = 'Parent' AND Active;");
    while ($user = $sql->fetchColumn()) {

      if ($squadFeeRequired) {
        // Calculate squad fees payable
        $getSquadMetadata->execute([$user]);

        $numMembers = 0;
        $discount = 0;

        while ($swimmerRow = $getSquadMetadata->fetch(PDO::FETCH_ASSOC)) {
          if (!bool($swimmerRow['ClubPays'])) {
            $numMembers++;
          }
          $metadata = [
            "PaymentType" => "SquadFees",
            "type" => [
              "object" => 'SquadFee',
              "id" => $swimmerRow['SquadID'],
              "name" => $swimmerRow['SquadName']
            ]
          ];

          $fee = BigDecimal::of((string) $swimmerRow['SquadFee'])->withPointMovedRight(2)->toInt();

          $metadata = json_encode($metadata);

          $addToPaymentsPending->execute([
            $date,
            $user,
            $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'] . ' - ' . $swimmerRow['SquadName'] . ' Squad Fees',
            $fee,
            $metadata
          ]);

          $paymentID = $db->lastInsertId();

          $track_info = [
            $mid,
            $swimmerRow['MemberID'],
            $user,
            'Squad Fees (' . $swimmerRow['SquadName'] . ')',
            $fee,
            'SquadFee',
            $paymentID
          ];
          $track->execute($track_info);

          if (!bool($swimmerRow['ClubPays']) && bool(env('IS_CLS'))) {
            // Calculate discounts if required.
            $swimmerDiscount = 0;
            $discountPercent = '0';
            if ($numMembers == 3) {
              // 20% discount applies
              $swimmerDiscount = (int) $fee*0.20;
              $discountPercent = '20';
            } else if ($numMembers > 3) {
              // 40% discount applies
              $swimmerDiscount = (int) $fee*0.40;
              $discountPercent = '40';
            }

            if ($swimmerDiscount > 0) {
              // Apply credit to account for discount
              $metadata = [
                "type" => [
                  "object" => 'SquadFee',
                  "id" => $swimmerRow['SquadID'],
                  "name" => $swimmerRow['SquadName']
                ]
              ];

              $addCreditToPaymentsPending->execute([
                $date,
                $user,
                $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'] . ' - Multi swimmer squad fee discount (' . $discountPercent . '%)',
                $swimmerDiscount,
                json_encode($metadata)
              ]);
            }
            //$discount += $swimmerDiscount;
          }

          if (bool($swimmerRow['ClubPays']))  {
            // Add a credit covering fees if club pays
            $addCreditToPaymentsPending->execute([
              $date,
              $user,
              $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'] . ' - ' . $swimmerRow['SquadName'] . ' Squad Fee Exemption',
              $fee,
              null
            ]);
          }
        }

        /*
        if (bool(env('IS_CLS')) && $discount > 0) {
          // Apply credit to account for discount
          $addCreditToPaymentsPending->execute([
            $date,
            $user,
            'Multi swimmer squad fee discount',
            $discount,
            null
          ]);
        }
        */
      }

      // Now calculate extra fees payable
      $getExtraMetadata->execute([$user]);

      while ($swimmerRow = $getExtraMetadata->fetch(PDO::FETCH_ASSOC)) {
        if ($swimmerRow['ExtraFee'] > 0) {
          $metadata = [
            "PaymentType"         => "ExtraFees",
            "type" => [
              "object" => 'ExtraFee',
              "id" => $swimmerRow['ExtraID'],
              "name" => $swimmerRow['ExtraName']
            ]
          ];
    
          $metadata = json_encode($metadata);

          $description = $swimmerRow['MForename'] . " " . $swimmerRow['MSurname'] . ' - ' . $swimmerRow['ExtraName'];
          $fee = BigDecimal::of((string) $swimmerRow['ExtraFee'])->withPointMovedRight(2)->toInt();

          if ($swimmerRow['Type'] == 'Payment') {
            $addToPaymentsPending->execute([
              $date,
              $user,
              $description,
              $fee,
              $metadata
            ]);
          } else if ($swimmerRow['Type'] == 'Refund') {
            $addCreditToPaymentsPending->execute([
              $date,
              $user,
              $description,
              $fee,
              $metadata
            ]);
          }

          $paymentID = $db->lastInsertId();

          $track_info = [
            $mid,
            $swimmerRow['MemberID'],
            $user,
            'Extra Fees (' . $swimmerRow['ExtraName'] . ')',
            $fee,
            'ExtraFee',
            $paymentID
          ];

          $track->execute($track_info);
        }
      }

      // Begin to sum up payments
      $getMonthSum->execute([$user, $date, 'Payment']);
      $amount = $getMonthSum->fetchColumn();

      $getMonthSum->execute([$user, $date, 'Refund']);
      $userDiscount = $getMonthSum->fetchColumn();

      $amount = $amount - $userDiscount;

      $getCountPending->execute([$user]);

      $dateString = date("F Y", strtotime("first day of this month")) . " DD";
      // If amount is too low, it will wait for the next payment round
      if ($amount > 100) {
        $addPaymentForCharge->execute([
          $date,
          'pending_api_request',
          $user,
          $dateString,
          $amount,
          null,
          null
        ]);
        $id = $db->lastInsertId();
        $setPaymentsPending->execute([
          $id,
          'Queued',
          $user,
          $date
        ]);
      } else if ($amount == 0 && $getCountPending->fetchColumn() > 0) {
        $addPaymentForCharge->execute([
          $date,
          'paid_out',
          $user,
          $dateString,
          $amount,
          null,
          null
        ]);
        $id = $db->lastInsertId();
        $setKey = $db->prepare("UPDATE payments SET PMkey = ? WHERE PaymentID = ?");
        $setKey->execute([
          'NA' . $id,
          $id
        ]);
        $setPaymentsPending->execute([
          $id,
          'Paid',
          $user,
          $date
        ]);

        // Set PM key - NOT NEEDED SOON
        $setPaymentsPendingPM->execute([
          'NA' . $id,
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
            BigDecimal::of((string) $row['SquadFee'])->withPointMovedRight(2)->toInt(),
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
          BigDecimal::of((string) $row['ExtraFee'])->withPointMovedRight(2)->toInt(),
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
