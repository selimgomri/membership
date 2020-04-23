<?php

$systemInfo = app()->system;

$feeType = $systemInfo->getSystemOption('ClubFeesType');

try {

  $systemInfo->setSystemOption('ClubFeeUpgradeType', $_POST['upgrade']);

  if ($feeType == 'NSwimmers') {
    $feesArray = json_decode($systemInfo->getSystemOption('ClubFeeNSwimmers'), true);
    $updatedFees = [];
    for ($i = 0; $i < sizeof($feesArray)+1; $i++) {
      if (isset($_POST[$i+1 . '-swimmers']) && (int) ($_POST[$i+1 . '-swimmers']*100) > 0) {
        $updatedFees[] = (int) ($_POST[$i+1 . '-swimmers']*100);
      } else {
        break;
      }
    }
    $systemInfo->setSystemOption('ClubFeeNSwimmers', json_encode($updatedFees));
  }
  $_SESSION['Update-Success'] = true;
} catch (Exception $e) {
  $_SESSION['Update-Error'] = true;
}

header("Location: " . autoUrl("settings/fees/membership-fees"));