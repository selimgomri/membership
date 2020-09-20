<?php

use function GuzzleHttp\json_encode;

if (!isset($_SERVER['HTTP_ACCEPT']) || $_SERVER['HTTP_ACCEPT'] != 'application/json') {
  halt(404);
}

$json = [
  'status' => 200,
  'error' => 'No errors',
];

// Check details for this session

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

try {

  if (!isset($_POST['member-id']) || !isset($_POST['session-id']) || !isset($_POST['session-date'])) throw new Exception('Missing form data');

  $date = null;
  try {
    $date = new DateTime($_POST['session-date'], new DateTimeZone('Europe/London'));
  } catch (Exception $e) {
    throw new Exception('Invalid date');
  }

  $now = new DateTime('now', new DateTimeZone('Europe/London'));

  // Get session
  $getSession = $db->prepare("SELECT `SessionID`, `SessionName`, `DisplayFrom`, `DisplayUntil`, `StartTime`, `EndTime`, `VenueName`, `Location`, `SessionDay`, `MaxPlaces`, `AllSquads` FROM `sessionsBookable` INNER JOIN `sessions` ON sessionsBookable.Session = sessions.SessionID INNER JOIN `sessionsVenues` ON `sessions`.`VenueID` = `sessionsVenues`.`VenueID` WHERE `sessionsBookable`.`Session` = ? AND `sessionsBookable`.`Date` = ? AND `sessions`.`Tenant` = ? AND DisplayFrom <= ? AND DisplayUntil >= ?");
  $getSession->execute([
    $_POST['session-id'],
    $date->format('Y-m-d'),
    $tenant->getId(),
    $date->format('Y-m-d'),
    $date->format('Y-m-d'),
  ]);

  $session = $getSession->fetch(PDO::FETCH_ASSOC);

  if (!$session) {
    throw new Exception('Session not found');
  }

  // Validate session happens on this day
  $dayOfWeek = $date->format('w');

  if ($session['SessionDay'] != $dayOfWeek) {
    throw new Exception('Session not found');
  }

  $numFormatter = new NumberFormatter("en-GB", NumberFormatter::SPELLOUT);

  // Validate member exists and belongs to user

  $getMember = $db->prepare("SELECT MForename, MSurname, MemberID, UserID FROM members WHERE MemberID = ?");
  $getMember->execute([
    $_POST['member-id'],
  ]);

  $member = $getMember->fetch(PDO::FETCH_ASSOC);

  // If no member not found
  if (!$member) throw new Exception('Member not found');

  // If unauthorised not found
  if (!$user->hasPermission('Admin') && !$user->hasPermission('Coach') && $member['UserID'] != $user->getId()) throw new Exception('Member not found');

  // Verify member can book on but allow admins and coaches to add anyone
  if (!$user->hasPermission('Admin') && !$user->hasPermission('Coach') && !bool($session['AllSquads'])) {
    // Check in a squad allowed

    $getMemberCount = $db->prepare("SELECT COUNT(*) FROM squadMembers WHERE Member = ? AND Squad IN (SELECT `Squad` FROM `sessionsSquads` WHERE `Session` = ?)");
    $getMemberCount->execute([
      $_POST['member-id'],
      $session['SessionID'],
    ]);

    if ($getMemberCount->fetchColumn() == 0) {
      // Not in a squad for this session
      throw new Exception('Member is not in a squad for this session');
    }
  }

  $bookingPossible = true;

  // Number booked in
  $getBookedCount = $db->prepare("SELECT COUNT(*) FROM `sessionsBookings` WHERE `Session` = ? AND `Date` = ?");
  $getBookedCount->execute([
    $session['SessionID'],
    $date->format('Y-m-d'),
  ]);
  $bookedCount = $getBookedCount->fetchColumn();

  // Max number
  $maxNumber = PHP_INT_MAX;
  if ($session['MaxPlaces']) {
    $maxNumber = $session['MaxPlaces'];
  }

  $placesAvailable = $maxNumber - $bookedCount;

  if ($placesAvailable == 0) {
    $bookingPossible = false;
    throw new Exception('No spaces available to book');
  }

  // Book a space for the member

  $addToBookings = $db->prepare("INSERT INTO `sessionsBookings` (`Session`, `Date`, `Member`, `BookedAt`) VALUES (?, ?, ?, ?)");
  $addToBookings->execute([
    $session['SessionID'],
    $date->format('Y-m-d'),
    $member['MemberID'],
    $now->format('Y-m-d H:i:s'),
  ]);



  // $sessionDateTime = DateTime::createFromFormat('Y-m-d-H:i:s', $date->format('Y-m-d') .  '-' . $session['StartTime']);
  // $startTime = new DateTime($session['StartTime'], new DateTimeZone('UTC'));
  // $endTime = new DateTime($session['EndTime'], new DateTimeZone('UTC'));
  // $duration = $startTime->diff($endTime);
  // $hours = (int) $duration->format('%h');
  // $mins = (int) $duration->format('%i');

  // $getCoaches = $db->prepare("SELECT Forename fn, Surname sn, coaches.Type code FROM coaches INNER JOIN users ON coaches.User = users.UserID WHERE coaches.Squad = ? ORDER BY coaches.Type ASC, Forename ASC, Surname ASC");

  // $getSessionSquads = $db->prepare("SELECT SquadName, ForAllMembers, SquadID FROM `sessionsSquads` INNER JOIN `squads` ON sessionsSquads.Squad = squads.SquadID WHERE sessionsSquads.Session = ? ORDER BY SquadFee DESC, SquadName ASC;");
  // $getSessionSquads->execute([
  //   $session['SessionID'],
  // ]);
  // $squadNames = $getSessionSquads->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {

  $message = $e->getMessage();
  if (get_class($e) == 'PDOException') {
    $message = 'A database error occurred';
  }

  $json['status'] = 500;
  $json['error'] = $message;
}

http_response_code(200);
header('content-type: application/json');
echo json_encode($json);
