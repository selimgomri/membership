<?php

/**
 * This webhook cron job moves members to new squads on their set day
 * 
 * It also adds members to a renewal on the day it starts
 */


$db = app()->db;

// Add renewal members to database
$date = new DateTime('now', new DateTimeZone('Europe/London'));

// Select open membership renewals
$getRenewals = $db->prepare("SELECT ID, Tenant FROM renewals WHERE StartDate <= :today AND EndDate >= :today;");
$getRenewals->execute([
  'today' => $date->format('Y-m-d')
]);

$getNumMembers = $db->prepare("SELECT COUNT(*) FROM renewalMembers WHERE RenewalID = ?");
$leavers = app()->tenant->getKey('LeaversSquad');

if ($leavers != null) {
  // Delete leavers
  $deleteFromLeavers = $db->prepare("DELETE FROM members WHERE SquadID = ?");
  $db->beginTransaction();

  try {
    $deleteFromLeavers->execute([
      $leavers
    ]);
    $db->commit();
    echo "Members in leavers deleted<br>";
  } catch (Exception $e) {
    // reportError($e);
    $db->rollBack();
  }
}

// Make sure we don't add members from leaver squad to renewal
$getMembers = $db->prepare("SELECT MemberID FROM members WHERE RR = 0 AND Tenant = ?;");

$addMember = $db->prepare("INSERT INTO renewalMembers (MemberID, RenewalID, CountRenewal) VALUES (?, ?, ?)");

$db->beginTransaction();

try {
  while ($renewal = $getRenewals->fetch(PDO::FETCH_ASSOC)) {
    
    // Check number of members for renewal

    // If no members added to a renewal,
    $getNumMembers->execute([$renewal['ID']]);
    if ($getNumMembers->fetchColumn() == 0) {
      $getMembers->execute([$renewal['Tenant']]);
      // Get members to add to renewal
      while ($member = $getMembers->fetchColumn()) {
        $addMember->execute([
          $member,
          $renewal,
          true
        ]);
      }
    }
  }
  $db->commit();
  echo "Renewal op success<br>";
} catch (Exception $e) {
  reportError($e);
  $db->rollBack();
}
