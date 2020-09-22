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

  $now = new DateTime('now', new DateTimeZone('UTC'));

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

  $sessionDateTime = DateTime::createFromFormat('Y-m-d-H:i:s', $date->format('Y-m-d') .  '-' . $session['StartTime'], new DateTimeZone('Europe/London'));
  $sessionEndDateTime = DateTime::createFromFormat('Y-m-d-H:i:s', $date->format('Y-m-d') .  '-' . $session['EndTime'], new DateTimeZone('Europe/London'));

  $bookingCloses = clone $sessionDateTime;
  $bookingCloses->modify('-15 minutes');

  $now = new DateTime('now', new DateTimeZone('Europe/London'));

  $bookingClosed = $now > $bookingCloses;

  if ($bookingClosed) {
    throw new Exception('Booking has closed');
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

  // Check not already booked

  $getBookingCount = $db->prepare("SELECT COUNT(*) FROM `sessionsBookings` WHERE `Session` = ? AND `Date` = ? AND `Member` = ?");
  $getBookingCount->execute([
    $session['SessionID'],
    $date->format('Y-m-d'),
    $member['MemberID'],
  ]);

  if ($getBookingCount->fetchColumn() > 0) {
    throw new Exception($member['MForename'] . ' is already booked onto this session');
  }

  // Book a space for the member

  $addToBookings = $db->prepare("INSERT INTO `sessionsBookings` (`Session`, `Date`, `Member`, `BookedAt`) VALUES (?, ?, ?, ?)");
  $addToBookings->execute([
    $session['SessionID'],
    $date->format('Y-m-d'),
    $member['MemberID'],
    $now->format('Y-m-d H:i:s'),
  ]);

  // $sessionDateTime = DateTime::createFromFormat('Y-m-d-H:i:s', $date->format('Y-m-d') .  '-' . $session['StartTime'], new Da);
  // $startTime = new DateTime($session['StartTime'], new DateTimeZone('UTC'));
  // $endTime = new DateTime($session['EndTime'], new DateTimeZone('UTC'));
  $duration = $sessionDateTime->diff($sessionEndDateTime);
  $hours = (int) $duration->format('%h');
  $mins = (int) $duration->format('%i');

  $getCoaches = $db->prepare("SELECT Forename fn, Surname sn, coaches.Type code FROM coaches INNER JOIN users ON coaches.User = users.UserID WHERE coaches.Squad = ? ORDER BY coaches.Type ASC, Forename ASC, Surname ASC");

  $getSessionSquads = $db->prepare("SELECT SquadName, ForAllMembers, SquadID FROM `sessionsSquads` INNER JOIN `squads` ON sessionsSquads.Squad = squads.SquadID WHERE sessionsSquads.Session = ? ORDER BY SquadFee DESC, SquadName ASC;");
  $getSessionSquads->execute([
    $session['SessionID'],
  ]);
  $squadNames = $getSessionSquads->fetchAll(PDO::FETCH_ASSOC);

  // Generate a confirmation email
  // Get member / user details

  $getUser = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id, Forename ufn, Surname usn, EmailAddress email FROM members INNER JOIN users ON users.UserID = members.UserID WHERE members.MemberID = ?");
  $getUser->execute([
    $member['MemberID'],
  ]);

  $emailUser = $getUser->fetch(PDO::FETCH_ASSOC);

  if ($emailUser) {

    try {

      $subject = 'Session Booking Confirmation - ' . $session['SessionName'] . ', ' . $sessionDateTime->format('H:i, j Y T');
      $username = $emailUser['ufn'] . ' ' . $emailUser['usn'];
      $emailAddress = $emailUser['email'];
      $content = '<p>Hello ' . htmlspecialchars($username) . ',</p>';
      $content .= '<p>This is confirmation of the following session booking;</p>';

      $content .= '<dl>';

      $content .= '<dt>Member</dt><dd>' . htmlspecialchars($emailUser['fn'] . ' ' . $emailUser['sn']) . '</dd>';
      $content .= '<dt>Session</dt><dd>' . htmlspecialchars($session['SessionName']) . '</dd>';
      $content .= '<dt>Date and time</dt><dd>' . htmlspecialchars($sessionDateTime->format('H:i, l j F Y T')) . '</dd>';
      $content .= '<dt>End time</dt><dd>' . htmlspecialchars($sessionEndDateTime->format('H:i')) . '</dd>';

      // Duration string
      $durationString = '';
      if ($hours > 0) {
        $durationString .= $hours . ' hour';
        if ($hours > 1) {
          $durationString .= 's';
        }
      }
      if ($mins > 0) {
        $durationString .= $mins . ' minute';
        if ($mins > 1) {
          $durationString .= 's';
        }
      }

      $content .= '<dt>Duration</dt><dd>' . htmlspecialchars($durationString) . '</dd>';

      // Coaches
      // $content .= '<dt>Duration</dt><dd>' . htmlspecialchars($durationString) . '</dd>';
      for ($i = 0; $i < sizeof($squadNames); $i++) {
        $getCoaches->execute([
          $squadNames[$i]['SquadID'],
        ]);
        $coaches = $getCoaches->fetchAll(PDO::FETCH_ASSOC);

        $content .= '<dt>' . htmlspecialchars($squadNames[$i]['SquadName']) . ' Coach';

        if (sizeof($coaches) > 0) {
          $content .= 'es';
        }

        $content .= '</dt><dd><ul style="mb-0">';

        for ($i = 0; $i < sizeof($coaches); $i++) {
          $content .= '<li><strong>' . htmlspecialchars($coaches[$i]['fn'] . ' ' . $coaches[$i]['sn']) . '</strong>, ' . htmlspecialchars(coachTypeDescription($coaches[$i]['code'])) . '</li>';
        }
        if (sizeof($coaches) == 0) {
          $content .= '<li>None assigned</li>';
        }

        $content .= '</ul></dd>';
      }

      $content .= '<dt>Location</dt><dd>' . htmlspecialchars($session['VenueName']) . ', <em>' . htmlspecialchars($session['Location']) . '</em></dd>';
      $content .= '<dt>Session Unique ID</dt><dd>' . htmlspecialchars($sessionDateTime->format('Y-m-d')) . '-S' . htmlspecialchars($session['SessionID']) . '</dd>';

      $content .= '</dl>';

      $content .= '<p>Penalties may apply for non-attendance.</p>';
      $content .= '<p>If you need to cancel your booking, please contact the person running this session or a member of club staff as soon as possible.</p>';

      notifySend(null, $subject, $content, $username, $emailAddress);
    } catch (Exception $e) {
      // Ignore failed send
    }
  }
} catch (Exception $e) {

  reportError($e);

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
