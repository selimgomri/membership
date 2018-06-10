<?php
$user = $_SESSION['UserID'];

$sql = "SELECT `Forename`, `Surname`, `EmailAddress` FROM `users` WHERE `UserID` = $user ;";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$redirectFlow = $client->redirectFlows()->create([
    "params" => [
        // This will be shown on the payment pages
        "description" => "Club fees",
        // Not the access token
        "session_token" => session_id(),
        "success_redirect_url" => autoUrl("payments/setup/1"),
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
