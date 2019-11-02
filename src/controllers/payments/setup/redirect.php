<?php

global $db;

$user = $_SESSION['UserID'];

$url_path = "payments";
if ($renewal_trap) {
  $url_path = "renewal/payments";
}

$selectSchedule = $db->prepare("SELECT COUNT(*) FROM `paymentSchedule` WHERE `UserID` = ?");
$selectSchedule->execute([$_SESSION['UserID']]);

if ($selectSchedule->fetchColumn() == 0) {
  header("Location: " . autoUrl("payments/setup/0"));
} else {
  try {
    $redirectFlowId = $_REQUEST['redirect_flow_id'];
    $redirectFlow = $client->redirectFlows()->complete(
      $_SESSION['GC_REDIRECTFLOW_ID'], //The redirect flow ID from above.
      ["params" => ["session_token" => $_SESSION['Token']]]
    );

    $mandate = $redirectFlow->links->mandate;
    $customer = $redirectFlow->links->customer;
    $bankAccount = $redirectFlow->links->customer_bank_account;

    $bank = $client->customerBankAccounts()->get($bankAccount);
    $accHolderName = $bank->account_holder_name;
    $accNumEnd = $bank->account_number_ending;
    $bankName = $bank->bank_name;

    $insertToMandates = $db->prepare("INSERT INTO `paymentMandates` (`UserID`, `Name`, `Mandate`, `Customer`, `BankAccount`, `BankName`, `AccountHolderName`, `AccountNumEnd`, `InUse`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertToMandates->execute([
      $_SESSION['UserID'],
      'Mandate',
      $mandate,
      $customer,
      $bankAccount,
      $bankName,
      $accHolderName,
      $accNumEnd,
      true
    ]);

    // If there is no preferred mandate existing, automatically set it to the one we've just added
    $getPreferredCount = $db->prepare("SELECT COUNT(*) FROM `paymentPreferredMandate` WHERE `UserID` = ?");
    $getPreferredCount->execute([$_SESSION['UserID']]);
    if ($getPreferredCount->fetchColumn() == 0) {
      $getMandateId = $db->prepare("SELECT `MandateID` FROM `paymentMandates` WHERE `Mandate` = ?");
      $getMandateId->execute([$mandate]);
      if ($mandateId = $getMandateId->fetchColumn()) {
        $insertPreferred = $db->prepare("INSERT INTO `paymentPreferredMandate` (`MandateID`, `UserID`) VALUES (?, ?)");
        $insertPreferred->execute([$mandateId, $user]);
      }
    }

    $_SESSION['GC-Setup-Status'] = 'success';
    header("Location: " . autoUrl($url_path . "/setup/4"));
  } catch (\GoCardlessPro\Core\Exception\ApiException | \GoCardlessPro\Core\Exception\MalformedResponseException $e) {
    $_SESSION['GC-Setup-Status'] = $e->getType();
    header("Location: " . autoUrl($url_path . "/setup/4"));
  } catch (\GoCardlessPro\Core\Exception\ApiConnectionException $e) {
    halt(500);
  } catch (Exception $e) {
    halt(500);
  }
}