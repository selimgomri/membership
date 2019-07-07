<?php

global $systemInfo;

for ($i = 1; $i < 4; $i++) {
  if (isset($_POST['county-' . $i]) && is_numeric($_POST['county-' . $i]) && $_POST['county-' . $i] >= 0) {
    try {
      $systemInfo->setSystemOption('ASA-County-Fee-L' . $i, (int) ($_POST['county-' . $i]*100));
      $_SESSION['COUNTY-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['COUNTY-ERROR'] = true;
    }
  }
  
  if (isset($_POST['region-' . $i]) && is_numeric($_POST['region-' . $i]) && $_POST['region-' . $i] >= 0) {
    try {
      $systemInfo->setSystemOption('ASA-Regional-Fee-L' . $i, (int) ($_POST['region-' . $i]*100));
      $_SESSION['REGION-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['REGION-ERROR'] = true;
    }
  }
  
  if (isset($_POST['national-' . $i]) && is_numeric($_POST['national-' . $i]) && $_POST['national-' . $i] >= 0) {
    try {
      $systemInfo->setSystemOption('ASA-National-Fee-L' . $i, (int) ($_POST['national-' . $i]*100));
      $_SESSION['NATIONAL-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['NATIONAL-ERROR'] = true;
    }
  }
}

header("Location: " . currentUrl());