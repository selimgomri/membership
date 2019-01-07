<?php

global $db;

try {
  $query = $db->prepare("SELECT PMkey FROM payments WHERE UserID = ? ORDER BY `Date` DESC ");
  $query->execute([$_SESSION['UserID']]);

  $payment = $query->fetchColumn();

  header("Location: " . autoUrl("payments/statement/" . $payment));
} catch (Exception $e) {
  halt(404);
}
