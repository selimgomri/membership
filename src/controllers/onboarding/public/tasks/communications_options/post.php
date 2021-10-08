<?php

$db = app()->db;

$session = \SCDS\Onboarding\Session::retrieve($_SESSION['OnboardingSessionId']);

if ($session->status == 'not_ready') halt(404);

$user = $session->getUser();

$tenant = app()->tenant;

$logos = app()->tenant->getKey('LOGO_DIR');

$stages = $session->stages;

$tasks = \SCDS\Onboarding\Session::stagesOrder();

// Validate and update user info

$good = true;

if ($good) {
  // If all good,
  $update = $db->prepare("UPDATE `users` SET `EmailComms` = ?, `MobileComms` = ? WHERE `UserID` = ?");
  $update->execute([
    (int) isset($_POST['emailContactOK']),
    (int) isset($_POST['smsContactOK']),
    $user->getId(),
  ]);

  // Set complete
  $session->completeTask('communications_options');

  header('location: ' . autoUrl('onboarding/go'));
} else {
  $_SESSION['FormError'] = true;
  header('location: ' . autoUrl('onboarding/go/start-task'));
}
