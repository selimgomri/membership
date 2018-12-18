<?php

$url_path = "payments";
if ($renewal_trap) {
	$url_path = "renewal/payments";
}

$user = $_SESSION['UserID'];

$sql = "SELECT * FROM `paymentSchedule` WHERE `UserID` = '$user';";
$scheduleExists = mysqli_num_rows(mysqli_query($link, $sql));
if ($scheduleExists == 0) {
	header("Location: " . autoUrl("payments/setup/0"));
} else {

  $sql = "SELECT `Forename`, `Surname`, `EmailAddress` FROM `users` WHERE `UserID` = $user ;";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

  $_SESSION['Token'] = $_SESSION['UserID'] . "-" . rand(1000,9999);

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
