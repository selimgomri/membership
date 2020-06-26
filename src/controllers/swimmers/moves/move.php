<?php

$db = app()->db;

use function GuzzleHttp\json_encode;

header("content-type: application/json");

try {

  if (!\SCDS\CSRF::verify()) {
    throw new Exception('CSRF validation failure');
  }

  try {
    $member = new Member($_POST['member']);
  } catch (Exception $e) {
    throw new Exception('No such member under this tenant');
  }
  $tenant = app()->tenant;

  // Handle move logic
  if ($_POST['event'] == 'move') {
    // Verify squad
    $squadCount = $db->prepare("SELECT COUNT(*) FROM squads WHERE squads.SquadID = ? AND squads.Tenant = ?");
    $squadCount->execute([
      $_POST['leave'],
      $tenant->getId()
    ]);

    if ($squadCount->fetchColumn() == 0) {
      throw new Exception('Old squad does not exist at this tenant');
    }

    $squadCount->execute([
      $_POST['join'],
      $tenant->getId()
    ]);

    if ($squadCount->fetchColumn() == 0) {
      throw new Exception('New squad does not exist at this tenant');
    }

    // Check member is in leaving squad/not in new squad
    $getMembershipCount = $db->prepare("SELECT COUNT(*) FROM squadMembers WHERE Member = ? AND Squad = ?");

    // In leaving
    $getMembershipCount->execute([
      $member->getId(),
      $_POST['leave'],
    ]);
    if ($getMembershipCount->fetchColumn() == 0) {
      throw new Exception('Member is not in the squad they are leaving');
    }

    // Not in new
    $getMembershipCount->execute([
      $member->getId(),
      $_POST['join'],
    ]);
    if ($getMembershipCount->fetchColumn() != 0) {
      throw new Exception('Member is already in the you have selected to add them to');
    }

    // Check there are no existing moves
    $getCount = $db->prepare("SELECT COUNT(*) FROM squadMoves WHERE Member = :member AND Old = :old_squad OR New = :old_squad OR Old = :new_squad OR New = :new_squad");
    $getCount->execute([
      'member' => $_POST['member'],
      'old_squad' => $_POST['leave'],
      'new_squad' => $_POST['join'],
    ]);

    if ($getCount->fetchColumn() > 0) {
      throw new Exception('An existing squad move is already in place for this member and one or more of the squads selected for this new move. Cancel it first to make changes.');
    }

    // Proceed
    if (bool($_POST['move-when'])) {
      // Move on specified date
      $date = new DateTime('now', new DateTimeZone('Europe/London'));
      try {
        $date = new DateTime($_POST['move-date'], new DateTimeZone('Europe/London'));
      } catch (Exception $e) {
        // Date invalid
        throw new Exception('The date you provided is invalid');
      }

      // Add move to database
      $add = $db->prepare("INSERT INTO squadMoves (`Member`, `Date`, `Old`, `New`, `Paying`) VALUES (?, ?, ?, ?, ?)");
      $add->execute([
        $member->getId(),
        $date->format("Y-m-d"),
        $_POST['leave'],
        $_POST['join'],
        !bool($_POST['paying']),
      ]);
    } else {
      // Move right now
      $move = $db->prepare("UPDATE squadMembers SET Squad = ?, Paying = ? WHERE Member = ? AND Squad = ?");
      $move->execute([
        $_POST['join'],
        !bool($_POST['paying']),
        $member->getId(),
        $_POST['leave'],
      ]);
    }

    echo json_encode([
      'status' => 200,
      'success' => true,
    ]);
  } else if ($_POST['event'] == 'join') {
    // Verify squad
    $squadCount = $db->prepare("SELECT COUNT(*) FROM squads WHERE squads.SquadID = ? AND squads.Tenant = ?");
    $squadCount->execute([
      $_POST['join'],
      $tenant->getId()
    ]);

    if ($squadCount->fetchColumn() == 0) {
      throw new Exception('New squad does not exist at this tenant');
    }

    // Check member is not in new squad
    $getMembershipCount = $db->prepare("SELECT COUNT(*) FROM squadMembers WHERE Member = ? AND Squad = ?");

    // Not in new
    $getMembershipCount->execute([
      $member->getId(),
      $_POST['join'],
    ]);
    if ($getMembershipCount->fetchColumn() != 0) {
      throw new Exception('Member is already in the you have selected to add them to');
    }

    // Check there are no existing moves
    $getCount = $db->prepare("SELECT COUNT(*) FROM squadMoves WHERE Member = :member AND Old = :new_squad OR New = :new_squad");
    $getCount->execute([
      'member' => $_POST['member'],
      'new_squad' => $_POST['join'],
    ]);

    if ($getCount->fetchColumn() > 0) {
      throw new Exception('An existing squad move is already in place for this member the squad selected for this new move. Cancel it first to make changes.');
    }

    // Proceed
    if (bool($_POST['move-when'])) {
      // Move on specified date
      $date = new DateTime('now', new DateTimeZone('Europe/London'));
      try {
        $date = new DateTime($_POST['move-date'], new DateTimeZone('Europe/London'));
      } catch (Exception $e) {
        // Date invalid
        throw new Exception('The date you provided is invalid');
      }

      // Add move to database
      $add = $db->prepare("INSERT INTO squadMoves (`Member`, `Date`, `Old`, `New`, `Paying`) VALUES (?, ?, ?, ?, ?)");
      $add->execute([
        $member->getId(),
        $date->format("Y-m-d"),
        null,
        $_POST['join'],
        !bool($_POST['paying']),
      ]);
    } else {
      // Move right now
      $move = $db->prepare("INSERT INTO squadMembers (Squad, Member, Paying) VALUES (?, ?, ?)");
      $move->execute([
        $_POST['join'],
        $member->getId(),
        !bool($_POST['paying'])
      ]);
    }

    echo json_encode([
      'status' => 200,
      'success' => true,
    ]);
  } else if ($_POST['event'] == 'leave') {
    // Leave a squad

    // Verify squad
    $getCount = $db->prepare("SELECT COUNT(*) FROM squadMembers WHERE Member = ? AND Squad = ?");
    $getCount->execute([
      $_POST['member'],
      $_POST['leave'],
    ]);

    if ($getCount->fetchColumn() == 0) {
      throw new Exception('The member is not an existing member of the squad');
    }

    // Check there are no existing moves
    $getCount = $db->prepare("SELECT COUNT(*) FROM squadMoves WHERE Member = :member AND Old = :squad OR New = :squad");
    $getCount->execute([
      'member' => $_POST['member'],
      'squad' => $_POST['leave'],
    ]);

    if ($getCount->fetchColumn() > 0) {
      throw new Exception('An existing squad move is already in place for this squad and member. Cancel it first to make changes.');
    }

    if (!bool($_POST['move-when'])) {
      // Remove now
      $move = $db->prepare("DELETE FROM squadMembers WHERE Member = ? AND Squad = ?");
      $move->execute([
        $_POST['member'],
        $_POST['leave']
      ]);

      echo json_encode([
        'status' => 200,
        'success' => true,
      ]);
    } else {
      // Move on specified date
      $date = new DateTime('now', new DateTimeZone('Europe/London'));
      try {
        $date = new DateTime($_POST['move-date'], new DateTimeZone('Europe/London'));
      } catch (Exception $e) {
        // Date invalid
        throw new Exception('The date you provided is invalid');
      }

      // Add move to database
      $add = $db->prepare("INSERT INTO squadMoves (`Member`, `Date`, `Old`, `New`, `Paying`) VALUES (?, ?, ?, ?, ?)");
      $add->execute([
        $member->getId(),
        $date->format("Y-m-d"),
        $_POST['leave'],
        null,
        0,
      ]);

      echo json_encode([
        'status' => 200,
        'success' => true,
      ]);
    }

  }

} catch (Exception $e) {

  $message = $e->getMessage();
  if (get_class($e) == 'PDOException') {
    $message = 'A database error occurred.';
  }

  http_response_code(200);
  echo json_encode([
    'status' => 200,
    'success' => false,
    'error' => $e->getMessage()
  ]);

}