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

header("Location: " . autoUrl(""));