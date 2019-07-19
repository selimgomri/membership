<?php

global $db;

$updateTime = $db->prepare("UPDATE galaEntries SET FeeToPay = ? WHERE EntryID = ?");

$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
$rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];


try {
$entries = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE members.UserID = ? AND NOT Charged AND FeeToPay > 0");
$entries->execute([$_SESSION['UserID']]);
} catch (Exception $e) {
  pre($e);
}
$entry = $entries->fetch(PDO::FETCH_ASSOC);

pre($entry);

$payingEntries = [];

if ($entry != null) {
  do {
    $allGood = false;

    if (isset($_POST[$entry['EntryID'] . '-pay']) && $_POST[$entry['EntryID'] . '-pay']) {
      $fee = 0;
      if ($entry['GalaFeeConstant']) {
        $fee = (int) ($entry['FeeToPay']*100);
        $allGood = true;
      } else {
        if (isset($_POST[$entry['EntryID'] . '-pay']) && $_POST[$entry['EntryID'] . '-pay']) {
          $fee = (int) ($entry['FeeToPay']*100);
          $allGood = true;
          $updateTime->execute([
            number_format($entry['FeeToPay'], 2, '.', ''),
            $entry['EntryID']
          ]);
        }
      }

      $payingEntries += [$entry['EntryID'] => [
        'Amount' => $fee,
        'UserEnteredAmount' => !$entry['GalaFeeConstant'],
        'Gala' => $entry['GalaID'],
        'Member' => $entry['MemberID']
      ]];
    }


  } while ($entry = $entries->fetch(PDO::FETCH_ASSOC));
}

$_SESSION['PaidEntries'] = $payingEntries;

if (sizeof($payingEntries) > 0) {
  header("Location: " . autoUrl("galas/pay-for-entries/checkout"));
} else {
  header("Location: " . autoUrl("galas/pay-for-entries"));
}