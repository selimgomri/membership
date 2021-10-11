<?php

namespace SCDS\Memberships;

use DateTimeZone;
use stdClass;

class Batch
{

  public static function goToCheckout($batchId, $method)
  {
    $db = app()->db;

    $object = new stdClass();

    $getBatch = $db->prepare("SELECT membershipBatch.ID id, DueDate due, Total total, PaymentTypes payMethods, membershipBatch.User `user` FROM membershipBatch INNER JOIN users ON users.UserID = membershipBatch.User WHERE membershipBatch.ID = ? AND users.Tenant = ?");
    $getBatch->execute([
      $batchId,
      app()->tenant->getId(),
    ]);

    // membershipYear.ID yearId, Total total, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd

    $batch = $getBatch->fetch(\PDO::FETCH_OBJ);

    if (!$batch) throw new \Exception();

    // Get batch items
    $getBatchItems = $db->prepare("SELECT membershipBatchItems.ID id, membershipBatchItems.Membership membershipId, membershipBatchItems.Amount amount, membershipBatchItems.Notes notes, members.MemberID memberId, members.MForename firstName, members.MSurname lastName, members.ASANumber ngbId, clubMembershipClasses.Type membershipType, clubMembershipClasses.Name membershipName, clubMembershipClasses.Description membershipDescription, membershipYear.ID yearId, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd FROM membershipBatchItems INNER JOIN membershipYear ON membershipYear.ID = membershipBatchItems.Year INNER JOIN members ON members.MemberID = membershipBatchItems.Member INNER JOIN clubMembershipClasses ON clubMembershipClasses.ID = membershipBatchItems.Membership WHERE Batch = ?");
    $getBatchItems->execute([
      $batchId
    ]);

    $payMethods = json_decode($batch->payMethods);

    // CHECK IF ALREADY ASSIGNED
    $checkExists = $db->prepare("SELECT COUNT(*) FROM `memberships` WHERE `Member` = ? AND `Year` = ? AND `Membership` = ?");
    $deleteFromBatch = $db->prepare("DELETE FROM `membershipBatchItems` WHERE `ID` = ?");
    $batchTotal = $db->prepare("SELECT SUM(`Amount`) FROM `membershipBatchItems` WHERE `Batch` = ?");
    $updateBatch = $db->prepare("UPDATE `membershipBatch` SET `Total` = ? WHERE `ID` = ?");

    while ($item = $getBatchItems->fetch(\PDO::FETCH_OBJ)) {
      $checkExists->execute([
        $item->memberId,
        $item->yearId,
        $item->membershipId
      ]);

      if ($checkExists->fetchColumn()) {
        // Remove from batch because the membership is already assigned for this year
        $deleteFromBatch->execute([
          $item->id,
        ]);

        // Recalculate batch total
        $batchTotal->execute([
          $batchId,
        ]);
        $total = $batchTotal->fetchColumn();

        // Update batch total
        $updateBatch->execute([
          $total,
          $batchId,
        ]);
      }
    }

    // Reload list of items
    $getBatchItems->execute([
      $batchId
    ]);

    // Reload batch data
    $getBatch->execute([
      $batchId,
      app()->tenant->getId(),
    ]);
    $batch = $getBatch->fetch(\PDO::FETCH_OBJ);

    if ($method == 'card') {
      // Create a checkout_v1 session

      $checkoutSession = \SCDS\Checkout\Session::new([
        'user' => $batch->user,
        'amount' => $batch->total,
      ]);

      while ($item = $getBatchItems->fetch(\PDO::FETCH_OBJ)) {

        $description = $item->firstName . ' ' . $item->lastName;
        if ($item->membershipDescription) {
          $description .= "\r\n\r\n" . $item->membershipDescription;
        }
        if ($item->notes) {
          $description .= "\r\n\r\n" . $item->notes;
        }

        $checkoutSession->addItem([
          'name' => $item->firstName . ' ' . $item->lastName . ', ' . $item->membershipName,
          'description' => $description,
          'amount' => $item->amount,
          'sub_items' => [],
          'attributes' => [
            'type' => 'membership_batch_item',
            'id' => $item->id,
          ],
        ]);
      }

      $checkoutSession->metadata['return']['url'] = autoUrl('memberships');
      $checkoutSession->metadata['return']['instant'] = false;
      $checkoutSession->metadata['return']['buttonString'] = 'Return to batch information page';

      $checkoutSession->metadata['cancel']['url'] = autoUrl('memberships/batches/' . $batchId);

      $checkoutSession->save();

      $object->type = 'checkout';
      $object->checkoutSession = $checkoutSession;

      return $object;
    } else if ($method == 'dd') {
      // Instantly add all to pending and show success message
      $insert = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES (?, ?, ?, ?, ?, ?, ?)");

      $date = new \DateTime('now', new \DateTimeZone('Europe/London'));
      $date = $date->format('Y-m-d');

      $db->beginTransaction();

      try {

        while ($item = $getBatchItems->fetch(\PDO::FETCH_OBJ)) {

          $description = $item->firstName . ' ' . $item->lastName;
          if ($item->membershipDescription) {
            $description .= " - " . $item->membershipDescription;
          }
          if ($item->notes) {
            $description .= " - " . $item->notes;
          }

          $insert->execute([
            $date,
            'Pending',
            app()->user->getId(),
            mb_strimwidth($description, 0, 255),
            $item->amount,
            'GBP',
            'debit'
          ]);
        }

        // Call mark paid code from business logic

        $db->commit();
      } catch (\Exception $e) {
        $db->rollBack();
      }

      $object->type = 'dd';
    }
  }

  public static function completeBatch($batchId, $paymentInfo)
  {
    $db = app()->db;

    // Update the batch to say it is paid
    $updateBatch = $db->prepare("UPDATE membershipBatch SET Completed = ?, PaymentDetails = ? WHERE ID = ?");
    $updateBatch->execute([
      (int) true,
      $paymentInfo,
      $batchId,
    ]);

    // Check if the batch relates to am onboarding session
    try {
      $get = $db->prepare("SELECT id FROM onboardingSessions WHERE batch = ?");
      $get->execute([
        $batchId
      ]);
      $sessionId = $get->fetchColumn();

      if ($sessionId) {
        $session = \SCDS\Onboarding\Session::retrieve($sessionId);
        $session->completeTask('fees');
      }
    } catch (\Exception $e) {
      reportError($e);
    }

    $time = new \DateTime('now', new \DateTimeZone('UTC'));

    // Get batch items
    $getBatchItems = $db->prepare("SELECT membershipBatchItems.ID, membershipBatchItems.Batch, membershipBatchItems.Membership, membershipBatchItems.Member, membershipBatchItems.Amount, membershipBatchItems.Notes, members.MForename, members.MSurname, clubMembershipClasses.Name, membershipBatchItems.Year, membershipYear.StartDate, membershipYear.EndDate FROM ((((membershipBatchItems INNER JOIN members ON members.MemberID = membershipBatchItems.Member) INNER JOIN clubMembershipClasses ON clubMembershipClasses.ID = membershipBatchItems.Membership) INNER JOIN membershipBatch ON membershipBatch.ID = membershipBatchItems.Batch) INNER JOIN membershipYear ON membershipYear.ID = membershipBatchItems.Year) WHERE membershipBatch.ID = ?");
    $getBatchItems->execute([
      $batchId,
    ]);

    while ($batchItem = $getBatchItems->fetch(\PDO::FETCH_OBJ)) {
      // Add membership record!
      $addMembership = $db->prepare("INSERT INTO `memberships` (`Member`, `Year`, `Membership`, `Amount`, `StartDate`, `EndDate`, `Purchased`, `PaymentInfo`, `Notes`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");
      $addMembership->execute([
        $batchItem->Member,
        $batchItem->Year,
        $batchItem->Membership,
        $batchItem->Amount,
        $batchItem->StartDate,
        $batchItem->EndDate,
        $time->format('Y-m-d H:i:s'),
        $paymentInfo,
        $batchItem->Notes,
      ]);
    }
  }

  public static function completeDirectDebitBatch($batchId)
  {
    $db = app()->db;
    $tenant = app()->tenant;

    // Get batch items
    $getBatchItems = $db->prepare("SELECT membershipBatchItems.ID, membershipBatchItems.Batch, membershipBatchItems.Membership, membershipBatchItems.Member, membershipBatchItems.Amount, membershipBatchItems.Notes, members.MForename, members.MSurname, clubMembershipClasses.Name, membershipBatchItems.Year, membershipYear.StartDate, membershipYear.EndDate, membershipBatch.User FROM ((((membershipBatchItems INNER JOIN members ON members.MemberID = membershipBatchItems.Member) INNER JOIN clubMembershipClasses ON clubMembershipClasses.ID = membershipBatchItems.Membership) INNER JOIN membershipBatch ON membershipBatch.ID = membershipBatchItems.Batch) INNER JOIN membershipYear ON membershipYear.ID = membershipBatchItems.Year) WHERE membershipBatch.ID = ? AND members.Tenant = ?");
    $getBatchItems->execute([
      $batchId,
      $tenant->getId(),
    ]);

    $now = new \DateTime('now', new \DateTimeZone('Europe/London'));

    // Add items to pending
    $add = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES (?, ?, ?, ?, ?, ?, ?)");

    while ($item = $getBatchItems->fetch(\PDO::FETCH_OBJ)) {
      $add->execute([
        $now->format('Y-m-d'),
        'Pending',
        $item->User,
        $item->Amount,
        'GBP',
        'Payment',
      ]);
    }

    $paymentInfo = json_encode([
      'type' => 'direct_debit',
      'data' => []
    ]);
    Batch::completeBatch($batchId, $paymentInfo);
  }
}
