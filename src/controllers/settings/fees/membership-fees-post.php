<?php

$feeType = app()->tenant->getKey('ClubFeesType');

try {

  app()->tenant->setKey('ClubFeeUpgradeType', $_POST['upgrade']);

  if ($feeType == 'NSwimmers') {
    $feesArray = json_decode(app()->tenant->getKey('ClubFeeNSwimmers'), true);
    $updatedFees = [];
    for ($i = 0; $i < sizeof($feesArray)+1; $i++) {
      if (isset($_POST[$i+1 . '-swimmers']) && (int) ($_POST[$i+1 . '-swimmers']*100) > 0) {
        $updatedFees[] = (int) ($_POST[$i+1 . '-swimmers']*100);
      } else {
        break;
      }
    }
    app()->tenant->setKey('ClubFeeNSwimmers', json_encode($updatedFees));
  }
  $_SESSION['Update-Success'] = true;
} catch (Exception $e) {
  $_SESSION['Update-Error'] = true;
}

header("Location: " . autoUrl("settings/fees/membership-fees"));