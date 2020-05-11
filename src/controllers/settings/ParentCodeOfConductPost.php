<?php

if (isset($_POST['CodeOfConduct'])) {
  try {
    $systemInfo = app()->system;
    app()->tenant->setKey('ParentCodeOfConduct', $_POST['CodeOfConduct']);
    $_SESSION['PCC-SAVED'] = true;
  } catch (Exception $e) {
    $_SESSION['PCC-ERROR'] = true;
  }
}

header("Location: " . autoUrl("settings/codes-of-conduct/parent"));