<?php

use Respect\Validation\Validator as v;

$user = 					mysqli_real_escape_string($link, $_POST['user']);
$description =		mysqli_real_escape_string($link, $_POST['desc']);
$amount =					(int) (mysqli_real_escape_string($link, $_POST['amount'])*100);
$date = 					date("Y-m-d");

$sql = "SELECT * FROM `users` WHERE `UserID` = '$user' AND `AccessLevel` =
'$parent';";
if (mysqli_num_rows(mysqli_query($link, $sql)) == 1) {

	$sql = "INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES ('$date', 'Pending', '$user', '$description', $amount, 'GBP', 'Payment');";
	if (mysqli_query($link, $sql)) {
		$_SESSION['ErrorState'] = '<div class="alert alert-success"><strong>Successfully Added the Charge</strong> <br>
		The user will be billed on their next billing date.</div>';
	} else {
		$_SESSION['ErrorState'] = '<div class="alert alert-danger"><strong>An error occured</strong> <br>
		The charge could not be added.</div>';
	}

} else {
	$_SESSION['ErrorState'] = '<div class="alert alert-danger"><strong>The selected user could not be found</strong> <br>
	The charge could not be added.</div>';
}

header("Location: " . autoUrl("payments/newcharge"));
