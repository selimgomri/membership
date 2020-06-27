<?php

header("content-type: application/json");

try {
  $db = app()->db;
  $tenant = app()->tenant;

  if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Admin' && $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Coach') {
    throw new Exception('Incorrect permissions');
  }

  if ($_POST['operation'] == 'delete') {
    $verifyMove = $db->prepare("SELECT * FROM squadMoves INNER JOIN members ON squadMoves.Member = members.MemberID WHERE members.Tenant = ? AND squadMoves.ID = ?");
    $verifyMove->execute([
      $tenant->getId(),
      $_POST['move'],
    ]);

    $move = $verifyMove->fetch(PDO::FETCH_ASSOC);

    if (!$move) {
      throw new Exception('Move does not exist');
    }

    $deleteMove = $db->prepare("DELETE FROM squadMoves WHERE ID = ?");
    $deleteMove->execute([
      $_POST['move'],
    ]);

    echo (json_encode([
      'status' => 200,
      'success' => true,
    ]));
  } else {
    throw new Exception('Unsupported operation');
  }
} catch (PDOException $e) {
  echo (json_encode([
    'status' => 200,
    'success' => false,
    'error_message' => 'A database error occurred. Please try again later',
  ]));
} catch (Exception $e) {
  echo (json_encode([
    'status' => 200,
    'success' => false,
    'error_message' => $e->getMessage(),
  ]));
}
