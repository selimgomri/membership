<?php

$PaymentID;

global $db;

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

if ($_SESSION['Token' . $PaymentID] == $token) {
  $db->prepare("UPDATE payments SET Status='paid_manually' WHERE PMkey = ?")->execute([$PaymentID]);
  $db->prepare("UPDATE paymentsPending SET Status='Paid' WHERE PMkey = ?")->execute([$PaymentID]);
} else {
  halt(404);
}

unset($_SESSION['Token' . $PaymentID]);

header("Location: " . autoUrl("payments/history/statement/" . $PaymentID));
