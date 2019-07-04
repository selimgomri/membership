<?php

global $db;

try {
  $count = $db->prepare("SELECT COUNT(*) FROM linkedAccounts WHERE (`User` = :user AND LinkedUser = :switch) OR (`User` = :switch AND LinkedUser = :user)");
  $count->execute(['user' => $_SESSION['UserID'], 'switch' => $account]);

  if ($count->fetchColumn() > 0) {
    try {
      $login = new \CLSASC\Membership\Login($db);
      $login->setUser($account);
      if (isset($_COOKIE[COOKIE_PREFIX . 'AutoLogin']) && $_COOKIE[COOKIE_PREFIX . 'AutoLogin'] != "") {
        $login->stayLoggedIn();
      }
      $login->preventWarningEmail();
      global $currentUser;
      $currentUser = $login->login();
    } catch (Exception $e) {
      halt(403);
    }
  } else {
    halt(404);
  }
} catch (Exception $e) {
  halt(500);
}

header("Location: " . autoUrl(""));