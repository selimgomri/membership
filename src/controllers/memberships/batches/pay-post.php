<?php

$user = app()->user;
$db = app()->db;

$getBatch = $db->prepare("SELECT membershipBatch.ID id, membershipYear.ID yearId, DueDate due, Total total, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd, PaymentTypes payMethods FROM membershipBatch INNER JOIN membershipYear ON membershipBatch.Year = membershipYear.ID INNER JOIN users ON users.UserID = membershipBatch.User WHERE membershipBatch.ID = ? AND users.Tenant = ?");
$getBatch->execute([
  $id,
  app()->tenant->getId(),
]);

$batch = $getBatch->fetch(PDO::FETCH_OBJ);

if (!$batch) halt(404);

// Get batch items
$getBatchItems = $db->prepare("SELECT membershipBatchItems.ID id, membershipBatchItems.Membership membershipId, membershipBatchItems.Amount amount, membershipBatchItems.Notes notes, members.MForename firstName, members.MSurname lastName, members.ASANumber ngbId, clubMembershipClasses.Type membershipType, clubMembershipClasses.Name membershipName, clubMembershipClasses.Description membershipDescription FROM membershipBatchItems INNER JOIN members ON members.MemberID = membershipBatchItems.Member INNER JOIN clubMembershipClasses ON clubMembershipClasses.ID = membershipBatchItems.Membership WHERE Batch = ?");
$getBatchItems->execute([
  $id
]);

$payMethods = json_decode($batch->payMethods);

if (isset($_POST['pay-method'])) {
  if ($_POST['pay-method'] == 'card') {
    // Create a checkout_v1 session

    $checkoutSession = \SCDS\Checkout\Session::new([
      'user' => app()->user->getId(),
      'amount' => $batch->total,
    ]);

    while ($item = $getBatchItems->fetch(PDO::FETCH_OBJ)) {

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

      $checkoutSession->metadata['return']['url'] = autoUrl('memberships');
      $checkoutSession->metadata['return']['instant'] = false;
      $checkoutSession->metadata['return']['buttonString'] = 'Return to batch information page';

      $checkoutSession->metadata['cancel']['url'] = autoUrl('memberships/batches/' . $id);

      $checkoutSession->save();

      http_response_code(302);
      header("Location: " . $checkoutSession->getUrl());
    }
  } else if ($_POST['pay-method'] == 'dd') {
    // Instantly add all to pending and show success message
    $insert = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $date = new DateTime('now', new DateTimeZone('Europe/London'));
    $date = $date->format('Y-m-d');

    $db->beginTransaction();

    try {

      while ($item = $getBatchItems->fetch(PDO::FETCH_OBJ)) {

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
    } catch (Exception $e) {
      $db->rollBack();
    }

    http_response_code(302);
    header("Location: " . autoUrl('memberships/batches/' . $id));
  }
} else {
  halt(404);
}
