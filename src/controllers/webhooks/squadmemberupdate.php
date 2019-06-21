<?php

// Mandatory Startup Sequence to carry out squad updates
$moves = $db->query("SELECT * FROM `moves` WHERE MovingDate <= CURDATE()");
while ($move = $moves->fetch(PDO::FETCH_ASSOC)) {
  try {
    // Move the swimmer to their new squad
    $query = $db->prepare("UPDATE `members` SET `SquadID` = ? WHERE `MemberID` = ?");
    $query->execute([$move['SquadID'], $move['MemberID']]);

    // Delete the squad move from the database
    $query = $db->prepare("DELETE FROM `moves` WHERE `MemberID` = ?");
    $query->execute([$move['MemberID']]);
  }
  catch (Exception $e) {
    // Catch all exceptions and halt
    // This causes the cron handler to catch the issue
    halt(500);
  }
}
