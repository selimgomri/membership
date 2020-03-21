<?php

if (isset($_POST['leavers-squad'])) {
  try {
    global $systemInfo;
    $systemInfo->setSystemOption('LeaversSquad', $_POST['leavers-squad']);
    $_SESSION['PCC-SAVED'] = true;
  } catch (Exception $e) {
    $_SESSION['PCC-ERROR'] = true;
  }
}

header("Location: " . autoUrl("settings/leavers-squad"));