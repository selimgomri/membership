<?php

global $db;

unset($_SESSION['AssRegStage']);

try {
  $login = new \CLSASC\Membership\Login($db);
  $login->setUser($_SESSION['AssRegGuestUser']);
  $login->stayLoggedIn();
  $login->preventWarningEmail();
  global $currentUser;
  $currentUser = $login->login();
} catch (Exception $e) {
  halt(403);
}

unset($_SESSION['AssRegGuestUser']);

$requiresRegistration = $db->prepare("SELECT `RR` FROM users WHERE UserID = ?");
$requiresRegistration->execute([$_SESSION['UserID']]);

if (bool($requiresRegistration->fetchColumn())) {
  header("Location: " . autoUrl("onboarding/go"));
} else {
  header("Location: " . autoUrl(""));
}