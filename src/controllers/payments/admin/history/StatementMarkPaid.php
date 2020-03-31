<?php

global $db;

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

if ($_SESSION['Token' . $id] == $token) {
  $db->prepare("UPDATE payments SET Status='paid_manually' WHERE PaymentID = ?")->execute([$id]);
  $db->prepare("UPDATE paymentsPending SET `Status` = 'Paid' WHERE Payment = ?")->execute([$id]);
} else {
  halt(404);
}

unset($_SESSION['Token' . $id]);

header("Location: " . autoUrl("payments/statements/" . $id));
