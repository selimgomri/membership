<?php

global $db;

try {
  $getCard = $db->prepare("SELECT MethodID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
  $getCard->execute([
    $_SESSION['UserID'],
    $id
  ]);
  $card = $getCard->fetch(PDO::FETCH_ASSOC);

  if ($card == null) {
    halt(404);
  }

  $update = $db->prepare("UPDATE stripePayMethods SET Reusable = ? WHERE MethodID = ?");
  $update->execute([
    0,
    $card['MethodID']
  ]);

  $_SESSION['CardDeleted'] = true;
  header("Location: " . autoUrl("payments/cards"));
} catch (Exception $e) {
  halt(500);
}