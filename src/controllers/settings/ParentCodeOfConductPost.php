<?php

if (isset($_POST['CodeOfConduct'])) {
  try {
    global $systemInfo;
    $systemInfo->setSystemOption('ParentCodeOfConduct', $_POST['CodeOfConduct']);
    $_SESSION['PCC-SAVED'] = true;
  } catch (Exception $e) {
    $_SESSION['PCC-ERROR'] = true;
  }
}

header("Location: " . autoUrl("settings/codes-of-conduct/parent"));