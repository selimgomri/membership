<?php

global $db;

unset($_SESSION['AssRegStage']);

try {
  $login = \CLSASC\Membership\Login($db);
  $login->setUser($_SESSION['AssRegGuestUser']);
  $login->stayLoggedIn();
  $login->preventWarningEmail();
  $login->login();
} catch (Exception $e) {
  halt(403);
}

unset($_SESSION['AssRegGuestUser']);

header("Location: " . autoUrl(""));