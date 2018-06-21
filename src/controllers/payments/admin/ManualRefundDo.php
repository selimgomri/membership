<?php

require BASE_PATH . 'controllers/payments/GoCardlessSetup';

$user = 					mysqli_real_escape_string($link, $_POST['user']);
$description =		mysqli_real_escape_string($link, $_POST['desc']);
$amount =					(int) (mysqli_real_escape_string($link, $_POST['amount'])*100);
$date = 					date("Y-m-d");

$client;

$sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Refund');";

//$sql = "INSERT INTO `payments` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'pending_api_request', '$sumUID', '$dateString', '$amount', 'GBP', 'Payment');";


if (mysqli_query($link, $sql)) {
	$_SESSION['ErrorState'] = '<div class="alert alert-success"><strong>Successfully Requested a Refund</strong> <br>
	The user will be refunded as soon as possible.</div>';
} else {
	$_SESSION['ErrorState'] = '<div class="alert alert-danger"><strong>An error occured</strong> <br>
	The refund could not be handled.</div>';
}

header("Location: " . autoUrl("payments/newrefund"));