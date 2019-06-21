<?php

global $db;

$getCard = $db->prepare("SELECT `Name`, Last4, Brand, ExpMonth, ExpYear, Funding, PostCode, Line1, Line2, CardName FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
$getCard->execute([$_SESSION['UserID'], $id]);

$card = $getCard->fetch(PDO::FETCH_ASSOC);

if ($card == null) {
  halt(404);
}

$newName = trim($_POST['name']);

if ($newName != $card['Name'] && $newName != "" && $newName != null) {
  try {
    $update = $db->prepare("UPDATE stripePayMethods SET `Name` = ? WHERE ID = ?");
    $update->execute([$newName, $id]);
    $_SESSION['CardNameUpdate'] = true;
  } catch (Exception $e) {
    // Do nothing
  }
}

header("Location: " . currentUrl());