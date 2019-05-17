<?php

global $db;

$user = 					$_POST['user'];
$description =		$_POST['desc'];
$amount =					(int) ($_POST['amount']*100);
$date = 					date("Y-m-d");

$insert = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES (?, 'Pending', ?, ?, ?, 'GBP', 'Refund')");

try {
  $insert->execute([$date, $user, $description, $amount]);
  $_SESSION['ErrorState'] = '<div class="alert alert-success"><strong>Successfully Requested a Refund</strong> <br>
	The user will be refunded as soon as possible.</div>';
} catch (Exception $e) {
  $_SESSION['ErrorState'] = '<div class="alert alert-danger"><strong>An error occured</strong> <br>
	The refund could not be handled.</div>';
}

header("Location: " . autoUrl("payments/newrefund"));
