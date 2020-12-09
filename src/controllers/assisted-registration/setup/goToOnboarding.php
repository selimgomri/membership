<?php

$db = app()->db;
$tenant = app()->tenant;

unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegStage']);

try {
  $login = new \CLSASC\Membership\Login($db);
  $login->setUser($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser']);
  $login->stayLoggedIn();
  $login->preventWarningEmail();
  $currentUser = app()->user;
  $currentUser = $login->login();
} catch (Exception $e) {
  halt(403);
}

unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser']);

$requiresRegistration = $db->prepare("SELECT `RR` FROM users WHERE UserID = ?");
$requiresRegistration->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

if (bool($requiresRegistration->fetchColumn()) && $tenant->getBooleanKey('REQUIRE_FULL_REGISTRATION')) {
  header("Location: " . autoUrl("onboarding/go"));
} else {
  // Ensure RR is false
  $updateRR = $db->prepare("UPDATE `users` SET `RR` = ? WHERE UserID = ?");
  $updateRR->execute([
    0,
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
  ]);

  header("Location: " . autoUrl(""));
}
