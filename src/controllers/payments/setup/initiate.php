<?php

global $db;

$url_path = "payments";
if ($renewal_trap) {
	$url_path = "renewal/payments";
}

$user = $_SESSION['UserID'];

$scheduleExists = false;
try {
  $getPaySchdeule = $db->prepare("SELECT * FROM `paymentSchedule` WHERE `UserID` = ?");
  $getPaySchdeule->execute([$_SESSION['UserID']]);
  $scheduleExists = $getPaySchdeule->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  halt(500);
}

if ($scheduleExists == null) {
  header("Location: " . autoUrl($url_path . "/setup/0"));
} else {
  $getDetails = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE UserID = ?");
  $getDetails->execute([$_SESSION['UserID']]);
  $row = $getDetails->fetch(PDO::FETCH_ASSOC);

  $_SESSION['Token'] = hash('sha256', $_SESSION['UserID'] . "-" . rand(1000,9999));

  $redirectFlow = $client->redirectFlows()->create([
    "params" => [
      // This will be shown on the payment pages
      "description" => "Club fee payments",
      // Not the access token
      "session_token" => $_SESSION['Token'],
      "success_redirect_url" => autoUrl($url_path . "/setup/3"),
      // Optionally, prefill customer details on the payment page
      "prefilled_customer" => [
        "given_name" => $row['Forename'],
        "family_name" => $row['Surname'],
        "email" => $row['EmailAddress']
      ]
    ]
  ]);

  // Hold on to this ID - you'll need it when you
  // "confirm" the redirect flow later
  $_SESSION['GC_REDIRECTFLOW_ID'] = $redirectFlow->id;

  header("Location: " . $redirectFlow->redirect_url);
}
