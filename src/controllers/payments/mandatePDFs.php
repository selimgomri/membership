<?php

require "GoCardlessSetup.php";

if (!isset($mandate) || $mandate == "") {
  halt(400);
}

global $db;
$checkDetails = $db->prepare("SELECT `UserID` FROM `paymentMandates` WHERE `Mandate` = ?");
$checkDetails->execute([$mandate]);
$userID = $_SESSION['UserID'];

$mandateUser = $checkDetails->fetchColumn();

if ($mandateUser == null) {
  halt(404);
}

if ($mandateUser != $_SESSION['UserID'] && $_SESSION['AccessLevel'] != "Admin") {
  halt(404);
}

$access = $_SESSION['AccessLevel'];

try {
  $return = $client->mandatePdfs()->create([
    "params" => ["links" => ["mandate" => $mandate]]
  ]);

  header("Location: " . $return->url);
} catch (Exception $e) {
  halt(500);
}