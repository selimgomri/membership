<?php

use Brick\Money\Money;
use Brick\Math\BigDecimal;

global $systemInfo;

for ($i = 1; $i < 4; $i++) {
  if (isset($_POST['county-' . $i]) && is_numeric($_POST['county-' . $i]) && $_POST['county-' . $i] >= 0) {
    try {
      $systemInfo->setSystemOption('ASA-County-Fee-L' . $i, (string) BigDecimal::of((string) $_POST['county-' . $i])->withPointMovedRight(2)->getUnscaledValue());
      $_SESSION['COUNTY-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['COUNTY-ERROR'] = true;
    }
  }
  
  if (isset($_POST['region-' . $i]) && is_numeric($_POST['region-' . $i]) && $_POST['region-' . $i] >= 0) {
    try {
      $systemInfo->setSystemOption('ASA-Regional-Fee-L' . $i, (string) BigDecimal::of((string) $_POST['region-' . $i])->withPointMovedRight(2)->getUnscaledValue());
      $_SESSION['REGION-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['REGION-ERROR'] = true;
    }
  }
  
  if (isset($_POST['national-' . $i]) && is_numeric($_POST['national-' . $i]) && $_POST['national-' . $i] >= 0) {
    try {
      $systemInfo->setSystemOption('ASA-National-Fee-L' . $i, (string) BigDecimal::of((string) $_POST['national-' . $i])->withPointMovedRight(2)->getUnscaledValue());
      $_SESSION['NATIONAL-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['NATIONAL-ERROR'] = true;
    }
  }
}

header("Location: " . autoUrl("settings/fees/swim-england-fees"));