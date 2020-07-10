<?php

use Brick\Money\Money;
use Brick\Math\BigDecimal;

for ($i = 1; $i < 4; $i++) {
  if (isset($_POST['county-' . $i]) && is_numeric($_POST['county-' . $i]) && $_POST['county-' . $i] >= 0) {
    try {
      app()->tenant->setKey('ASA-County-Fee-L' . $i, (string) BigDecimal::of((string) $_POST['county-' . $i])->withPointMovedRight(2)->getUnscaledValue());
      $_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['TENANT-' . app()->tenant->getId()]['COUNTY-ERROR'] = true;
    }
  }
  
  if (isset($_POST['region-' . $i]) && is_numeric($_POST['region-' . $i]) && $_POST['region-' . $i] >= 0) {
    try {
      app()->tenant->setKey('ASA-Regional-Fee-L' . $i, (string) BigDecimal::of((string) $_POST['region-' . $i])->withPointMovedRight(2)->getUnscaledValue());
      $_SESSION['TENANT-' . app()->tenant->getId()]['REGION-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['TENANT-' . app()->tenant->getId()]['REGION-ERROR'] = true;
    }
  }
  
  if (isset($_POST['national-' . $i]) && is_numeric($_POST['national-' . $i]) && $_POST['national-' . $i] >= 0) {
    try {
      app()->tenant->setKey('ASA-National-Fee-L' . $i, (string) BigDecimal::of((string) $_POST['national-' . $i])->withPointMovedRight(2)->getUnscaledValue());
      $_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-SAVED'] = true;
    } catch (Exception $e) {
      $_SESSION['TENANT-' . app()->tenant->getId()]['NATIONAL-ERROR'] = true;
    }
  }
}

header("Location: " . autoUrl("settings/fees/swim-england-fees"));