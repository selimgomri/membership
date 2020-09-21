<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

if (!$user->hasPermission('Admin') && !$user->hasPermission('Coach')) {
  halt(404);
}

try {

  if (!isset($_POST['session']) && !isset($_POST['date'])) throw new Exception('Missing POST data');

  $date = new DateTime($_POST['date'], new DateTimeZone('Europe/London'));

  // Get session
  $getSession = $db->prepare("SELECT `SessionID`, `SessionName`, `DisplayFrom`, `DisplayUntil`, `StartTime`, `EndTime`, `VenueName`, `Location`, `SessionDay`, `MaxPlaces`, `AllSquads` FROM `sessionsBookable` INNER JOIN `sessions` ON sessionsBookable.Session = sessions.SessionID INNER JOIN `sessionsVenues` ON `sessions`.`VenueID` = `sessionsVenues`.`VenueID` WHERE `sessionsBookable`.`Session` = ? AND `sessionsBookable`.`Date` = ? AND `sessions`.`Tenant` = ? AND DisplayFrom <= ? AND DisplayUntil >= ?");
  $getSession->execute([
    $_POST['session'],
    $date->format('Y-m-d'),
    $tenant->getId(),
    $date->format('Y-m-d'),
    $date->format('Y-m-d'),
  ]);

  $session = $getSession->fetch(PDO::FETCH_ASSOC);

  if (!$session) {
    halt(404);
  }

  // Validate session happens on this day
  $dayOfWeek = $date->format('w');

  if ($session['SessionDay'] != $dayOfWeek) {
    throw new Exception('Date invalid for this session');
  }

  try {

    $updateBookable = $db->prepare("UPDATE `sessionsBookable` SET `MaxPlaces` = ?, `AllSquads` = ? WHERE `Session` = ? AND `Date` = ?");

    $maxPlaces = null;
    if (isset($_POST['number-limit']) && bool($_POST['number-limit']) && ((int) $_POST['max-count']) > 0) {
      $maxPlaces = ((int) $_POST['max-count']);
    }

    $allSquads = false;
    if (isset($_POST['open-to']) && bool($_POST['open-to'])) {
      $allSquads = true;
    }

    $updateBookable->execute([
      $maxPlaces,
      (int) $allSquads,
      $session['SessionID'],
      $date->format('Y-m-d'),
    ]);

    // Ensure register is clear

    $_SESSION['TENANT-' . app()->tenant->getId()]['EditRequireBookingSuccess'] = true;
    http_response_code(302);
    header("location: " . autoUrl('sessions/booking/edit?session=' . $session['SessionID'] . '&date=' . $date->format('Y-m-d')));
  } catch (Exception $e) {

    $message = $e->getMessage();
    if (get_class($e) == 'PDOException') {
      reportError($e);
      $message = 'A database error occurred';
    }

    $_SESSION['TENANT-' . app()->tenant->getId()]['RequireBookingError'] = $message;
    http_response_code(302);
    header("location: " . autoUrl('sessions/booking/edit?session=' . $session['SessionID'] . '&date=' . $date->format('Y-m-d')));
  }
} catch (Exception $e) {

  reportError($e);
  halt(404);
}
