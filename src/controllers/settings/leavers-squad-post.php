<?php

if (isset($_POST['leavers-squad'])) {
  try {
    $systemInfo = app()->system;
    app()->tenant->setKey('LeaversSquad', $_POST['leavers-squad']);
    $_SESSION['PCC-SAVED'] = true;
  } catch (Exception $e) {
    $_SESSION['PCC-ERROR'] = true;
  }
}

header("Location: " . autoUrl("settings/leavers-squad"));