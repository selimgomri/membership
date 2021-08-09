<?php

use Ramsey\Uuid\Uuid;
use Respect\Validation\Exceptions\NegativeException;

$db = app()->db;
$tenant = app()->tenant;

$getYear = $db->prepare("SELECT `Name`, `StartDate`, `EndDate` FROM `membershipYear` WHERE `ID` = ? AND `Tenant` = ?");
$getYear->execute([
  $id,
  $tenant->getId(),
]);
$year = $getYear->fetch(PDO::FETCH_ASSOC);

if (!$year) halt(404);

if (!isset($_GET['user'])) halt(404);

// Check user exists
$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, RR FROM users WHERE Tenant = ? AND UserID = ? AND Active");
$userInfo->execute([
  $tenant->getId(),
  $_GET['user']
]);

$info = $userInfo->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

$getMembers = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, NGBCategory ngb, ngbMembership.Name ngbName, ngbMembership.Fees ngbFees, ClubCategory club, clubMembership.Name clubName, clubMembership.Fees clubFees FROM members INNER JOIN clubMembershipClasses AS ngbMembership ON ngbMembership.ID = members.NGBCategory INNER JOIN clubMembershipClasses AS clubMembership ON clubMembership.ID = members.ClubCategory WHERE Active AND UserID = ? ORDER BY fn ASC, sn ASC");
$getMembers->execute([
  $_GET['user']
]);

$getCurrentMemberships = $db->prepare("SELECT `Name` `name`, `Description` `description`, `Type` `type`, `memberships`.`Amount` `paid`, `clubMembershipClasses`.`Fees` `expectPaid` FROM `memberships` INNER JOIN clubMembershipClasses ON memberships.Membership = clubMembershipClasses.ID WHERE `Member` = ? AND `Year` = ?");
$hasMembership = $db->prepare("SELECT COUNT(*) FROM memberships WHERE `Member` = ? AND `Year` = ? AND `Membership` = ?");

try {

  $items = [];
  $total = 0;
  $batchId = Uuid::uuid4();

  while ($member = $getMembers->fetch(PDO::FETCH_OBJ)) {
    $hasMembership->execute([
      $member->id,
      $id,
      $member->ngb,
    ]);
    $has = $hasMembership->fetchColumn() > 0;

    if (!$has && isset($_POST[$member->id . '-' . $member->ngb . '-yes'])) {
      $amount = (int) MoneyHelpers::decimalToInt($_POST[$member->id . '-' . $member->ngb . '-amount']);

      if ($amount < 0) throw new Exception('Negative number');

      $total += $amount;

      $items[] = [
        Uuid::uuid4(),
        $batchId,
        $member->ngb,
        $member->id,
        $amount,
        trim($_POST[$member->id . '-' . $member->ngb . '-notes']),
      ];
    }

    $hasMembership->execute([
      $member->id,
      $id,
      $member->club,
    ]);
    $has = $hasMembership->fetchColumn() > 0;

    if (!$has && isset($_POST[$member->id . '-' . $member->club . '-yes'])) {
      $amount = (int) MoneyHelpers::decimalToInt($_POST[$member->id . '-' . $member->club . '-amount']);

      if ($amount < 0) throw new Exception('Negative number');
      
      $total += $amount;

      $items[] = [
        Uuid::uuid4(),
        $batchId,
        $member->club,
        $member->id,
        $amount,
        trim($_POST[$member->id . '-' . $member->club . '-notes']),
      ];
    }
  }

  // Add batch info

  $dueDate = null;
  if (isset($_POST['due-date'])) {
    try {
      $dueDate = new DateTime($_POST['due-date'], new DateTimeZone('Europe/London'));
      $dueDate = $dueDate->format('Y-m-d');
    } catch (Exception $e) {
      // Ignore
      $dueDate = null;
    }
  }

  $paymentTypes = [];
  if (isset($_POST['payment-card']) && bool($_POST['payment-card'])) {
    $paymentTypes[] = 'card';
  }
  if (isset($_POST['payment-direct-debit']) && bool($_POST['payment-direct-debit'])) {
    $paymentTypes[] = 'dd';
  }

  $addBatch = $db->prepare("INSERT INTO `membershipBatch` (`ID`, `User`, `Year`, `StartText`, `Footer`, `DueDate`, `Total`, `PaymentTypes`, `PaymentDetails`, `AutoReminders`, `Creator`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");

  $addBatch->execute([
    $batchId,
    $_GET['user'],
    $id,
    trim($_POST['introduction-text']),
    trim($_POST['footer-text']),
    $dueDate,
    $total,
    json_encode($paymentTypes),
    json_encode([]),
    0,
    app()->user->getId(),
  ]);

  $addBatchItem = $db->prepare("INSERT INTO `membershipBatchItems` (`ID`, `Batch`, `Membership`, `Member`, `Amount`, `Notes`) VALUES (?, ?, ?, ?, ?, ?);");

  foreach ($items as $item) {
    $addBatchItem->execute($item);
  }

  http_response_code(302);
  header("location: " . autoUrl("users/" . $_GET['user']));
} catch (Exception $e) {
  reportError($e);

  http_response_code(302);
  header("location: " . autoUrl("memberships/years/$id/new-batch?user=" . $_GET['user']));
}
