<?php
  $_SESSION = array();
  session_destroy();
  setcookie("CLSASC_AutoLogin", "", 0, "/", 'chesterlestreetasc.co.uk', true, true);
  header("Location: " . autoUrl("") . "");
?>
