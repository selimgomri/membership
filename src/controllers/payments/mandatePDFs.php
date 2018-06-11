<?php

if (!isset($mandate) || $mandate == "") {
  halt(400);
}

$mandate = mysqli_real_escape_string($link, $mandate);

$userID = $_SESSION['UserID'];
$sql = "SELECT `UserID` FROM `paymentMandates` WHERE `Mandate` = '$mandate';";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) == 0) {
  halt(404);
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$mandateUserID = $row['UserID'];
$access = $_SESSION['AccessLevel'];

require "GoCardlessSetup.php";

if ($userID == $mandateUserID || $access == "Admin") {

  $return = $client->mandatePdfs()->create([
    "params" => ["links" => ["mandate" => $mandate]]
  ]);

  header("Location: " . $return->url);

} else {
  halt(403);
}
