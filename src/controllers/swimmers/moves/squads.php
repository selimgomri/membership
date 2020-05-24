<?php

use function GuzzleHttp\json_encode;

if (!app()->user->hasPermission('Admin') && !app()->user->hasPermission('Coach')) {
  halt(404);
}

$db = app()->db;
$tenant = app()->tenant;

header("content-type: application/json");

try {

  // Get member
  $member = $db->prepare("SELECT MemberID id FROM members WHERE MemberID = ? AND Tenant = ?");
  $member->execute([
    $id,
    $tenant->getId()
  ]);

  $member = $member->fetch(PDO::FETCH_ASSOC);

  // If member does not exist, return error json and exit
  if (!$member) {
    http_response_code(404);
    echo json_encode([
      'status' => 404,
      'message' => 'Not Found'
    ]);
    return;
  }

  // Need to get current squads, squads with planned moves and squads we can move to
  $member = new Member($id);

  // Current
  $currentSquads = $member->getSquads();

  // Move to
  $canMoveTo = $db->prepare("SELECT SquadName `name`, SquadID id FROM squads WHERE Tenant = :tenant AND SquadID NOT IN (SELECT Squad SquadID FROM squadMembers WHERE Member = :member) AND SquadID NOT IN (SELECT Old SquadID FROM squadMoves WHERE Member = :member UNION SELECT New SquadID FROM squadMoves WHERE Member = :member) ORDER BY squads.SquadFee DESC, SquadName ASC");
  $canMoveTo->execute([
    'tenant' => $tenant->getId(),
    'member' => $member->getId()
  ]);

  $current = [];
  foreach ($currentSquads as $squad) {
    $current[] = [
      'id' => $squad->getId(),
      'name' => $squad->getName()
    ];
  }

  echo json_encode([
    'current' => $current,
    'can_join' => $canMoveTo->fetchAll(PDO::FETCH_ASSOC)
  ]);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'status' => 500,
    'message' => $e->getMessage()
  ]);
}