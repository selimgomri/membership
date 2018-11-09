<?php
  $_SESSION = array();
  session_destroy();
  setcookie(COOKIE_PREFIX . "AutoLogin", "", 0, "/", 'chesterlestreetasc.co.uk', true, true);
  setcookie(COOKIE_PREFIX . "UserInformation", "", 0, "/", 'chesterlestreetasc.co.uk', true, true);
  header("Location: " . autoUrl("") . "");
?>
