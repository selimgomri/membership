<?php

$db = app()->db;

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

if (bool($requiresRegistration->fetchColumn())) {
  header("Location: " . autoUrl("onboarding/go"));
} else {
  header("Location: " . autoUrl(""));
}