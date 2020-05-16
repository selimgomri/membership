<?php

// Get members and squads
$members = $db->query("SELECT MemberID, SquadID FROM members");

$addNew = $db->prepare("INSERT INTO squadMembers (Member, Squad) VALUES (?, ?)");

// For each add to new table
while ($member = $members->fetch(PDO::FETCH_ASSOC)) {
  if ($member['SquadID']) {
    $addNew->execute([
      $member['MemberID'],
      $member['SquadID']
    ]);
  }
}

// Remove SquadID column from members
$db->query("ALTER TABLE members 
  DROP FOREIGN KEY `members_ibfk_1`,
  DROP INDEX `SquadID`,
  DROP SquadID;");