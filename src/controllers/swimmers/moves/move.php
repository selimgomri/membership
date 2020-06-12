<?php

$db = app()->db;

use function GuzzleHttp\json_encode;

header("content-type: application/json");

try {

  try {
    $member = new Member($_POST['member']);
  } catch (Exception $e) {
    throw new Exception('No such member');
  }
  $tenant = app()->tenant;

  // Handle move logic
  if ($_POST['event'] == 'move') {

  } else if ($_POST['event'] == 'join') {
    
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
      // Remove at a later date

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
    }

  }

} catch (Exception $e) {

  http_response_code(200);
  echo json_encode([
    'status' => 200,
    'success' => false,
    'error' => $e->getMessage()
  ]);

}