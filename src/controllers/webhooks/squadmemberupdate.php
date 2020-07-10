<?php

/**
 * This webhook cron job moves members to new squads on their set day
 * 
 * It also adds members to a renewal on the day it starts
 */


$db = app()->db;

// Mandatory Startup Sequence to carry out squad updates
$moves = $db->query("SELECT MemberID, SquadID FROM `moves` WHERE MovingDate <= CURDATE()");
$update = $db->prepare("UPDATE `members` SET `SquadID` = ? WHERE `MemberID` = ?");
$delete = $db->prepare("DELETE FROM `moves` WHERE `MemberID` = ?");
while ($move = $moves->fetch(PDO::FETCH_ASSOC)) {
  try {
    $db->beginTransaction();
    // Move the swimmer to their new squad
    $update->execute([$move['SquadID'], $move['MemberID']]);

    // Delete the squad move from the database
    $delete->execute([$move['MemberID']]);
    $db->commit();
    echo "Squad move op success<br>";
  }
  catch (Exception $e) {
    // Catch all exceptions and halt
    // This causes the cron handler to catch the issue
    reportError($e);
    $db->rollBack();
  }
}
echo "Squad move work complete<br>";

// Add renewal members to database
$date = new DateTime('now', new DateTimeZone('Europe/London'));

// Select open membership renewals
$getRenewals = $db->prepare("SELECT ID FROM renewals WHERE StartDate <= :today AND EndDate >= :today;");
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
$getMembers = null;
if ($leavers == null) {
  $getMembers = $db->query("SELECT MemberID FROM members WHERE RR = 0;");
} else {
  $getMembers = $db->prepare("SELECT MemberID FROM members WHERE RR = 0 AND SquadID != ?;");
  $getMembers->execute([$leavers]);
}

$addMember = $db->prepare("INSERT INTO renewalMembers (MemberID, RenewalID, CountRenewal) VALUES (?, ?, ?)");

$db->beginTransaction();

try {
  while ($renewal = $getRenewals->fetchColumn()) {
    // Check number of members for renewal

    // If no members added to a renewal,
    $getNumMembers->execute([$renewal]);
    if ($getNumMembers->fetchColumn() == 0) {
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