<?php

global $systemInfo;

$feeType = $systemInfo->getSystemOption('ClubFeesType');

try {

  $systemInfo->setSystemOption('ClubFeeUpgradeType', $_POST['upgrade']);

  if ($feeType == 'Family/Individual') {
    $family = true;
    $systemInfo->setSystemOption('ClubFeeIndividual', (int) ($_POST['indv']*100));
    $systemInfo->setSystemOption('ClubFeeFamily', (int) ($_POST['fam']*100));
  } else if ($feeType == 'PerMember') {
  } else if ($feeType == 'MonthlyPrecept') {
  } else if ($feeType == 'MonthlyPreceptFamily') {
  }
  $_SESSION['Update-Success'] = true;
} catch (Exception $e) {
  $_SESSION['Update-Error'] = true;
}

header("Location: " . currentUrl());