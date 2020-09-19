<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

if (!$user->hasPermission('Admin') || !$user->hasPermission('Coach')) {
  halt(404);
}

try {

  if (!isset($_POST['session']) && !isset($_POST['date'])) throw new Exception('Missing POST data');

  $date = new DateTime($_POST['date'], new DateTimeZone('Europe/London'));

  // Get session
  $getSession = $db->prepare("SELECT `SessionID`, `SessionName`, `DisplayFrom`, `DisplayUntil`, `StartTime`, `EndTime`, `VenueName`, `Location`, `SessionDay` FROM `sessions` INNER JOIN `sessionsVenues` ON `sessions`.`VenueID` = `sessionsVenues`.`VenueID` WHERE `sessions`.`SessionID` = ? AND `sessions`.`Tenant` = ? AND DisplayFrom <= ? AND DisplayUntil >= ?");
  $getSession->execute([
    $_POST['session'],
    $tenant->getId(),
    $date->format('Y-m-d'),
    $date->format('Y-m-d'),
  ]);

  $session = $getSession->fetch(PDO::FETCH_ASSOC);

  if (!$session) {
    throw new Exception('Session does not exist');
  }

  // Validate session happens on this day
  $dayOfWeek = $date->format('w');

  if ($session['SessionDay'] != $dayOfWeek) {
    throw new Exception('Date invalid for this session');
  }

  try {

    $addBookable = $db->prepare("INSERT INTO `sessionsBookable` (`Session`, `Date`, `MaxPlaces`, `AllSquads`) VALUES (?, ?, ?, ?)");

    $maxPlaces = null;
    if (isset($_POST['number-limit']) && bool($_POST['number-limit']) && ((int) $_POST['max-count']) > 0) {
      $maxPlaces = ((int) $_POST['max-count']);
    }

    $allSquads = false;
    if (isset($_POST['open-to']) && bool($_POST['open-to'])) {
      $allSquads = true;
    }

    $addBookable->execute([
      $session['SessionID'],
      $date->format('Y-m-d'),
      $maxPlaces,
      (int) $allSquads,
    ]);

    // Ensure register is clear

    // $_SESSION['TENANT-' . app()->tenant->getId()]['RequireBookingSuccess'] = $message;
    http_response_code(302);
    header("location: " . autoUrl('sessions/booking/book?session=' . $session['SessionID'] . '&date=' . $date->format('Y-m-d')));


    } catch (Exception $e) {

      $message = $e->getMessage();
      if (get_class($e) == 'PDOException') {
        $message = 'A database error occurred';
      }
 
      $_SESSION['TENANT-' . app()->tenant->getId()]['RequireBookingError'] = $message;
      http_response_code(302);
      header("location: " . autoUrl('sessions/booking/book?session=' . $session['SessionID'] . '&date=' . $date->format('Y-m-d')));

    }

} catch (Exception $e) {

  reportError($e);
  halt(404);

}
