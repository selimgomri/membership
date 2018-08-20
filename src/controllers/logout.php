<?php
  $_SESSION = array();
  session_destroy();
  setcookie("CLSASC_AutoLogin", "", 0, "/", app('request')->hostname, true, true);
  header("Location: " . autoUrl("") . "");
?>
