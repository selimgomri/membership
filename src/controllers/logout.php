<?php
  $user = $_SESSION['UserID'];

  $_SESSION = array();
  session_destroy();

  $secure = true;
  if (app('request')->protocol == 'http') {
    $secure = false;
  }

  if (bool(env('IS_CLS'))) {
    setcookie(COOKIE_PREFIX . "UserInformation", "", 0 , "/", 'chesterlestreetasc.co.uk', $secure, false);
  }
  setcookie(COOKIE_PREFIX . "AutoLogin", "", 0, "/", app('request')->hostname('request')->hostname, $secure, false);

  if (isset($_COOKIE[COOKIE_PREFIX . 'AutoLogin'])) {
    // Unset the hash.
    global $db;
    $unset = $db->prepare("UPDATE userLogins SET HashActive = ? WHERE Hash = ? AND UserID = ?");
    $unset->execute([
      0,
      $_COOKIE[COOKIE_PREFIX . 'AutoLogin'],
      $user
    ]);
  }

  header("Location: " . autoUrl(""));
?>
