<?php

use Respect\Validation\Validator as v;
global $db;

$user = 					$_POST['user'];
$description =		$_POST['desc'];
$amount =					(int) ($_POST['amount']*100);
$date = 					date("Y-m-d");

$checkUser = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `UserID` = ? AND `AccessLevel` = ?");
$checkUser->execute([$_POST['user'], 'Parent']);

if ($checkUser->fetchColumn() == 1) {
  try {
    $insertToDb = $db->prepare("INSERT INTO `paymentsPending` (`Date`, `Status`, `UserID`, `Name`, `Amount`, `Currency`, `Type`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertToDb->execute([
      $date,
      'Pending',
      $user,
      $description,
      $amount,
      'GBP',
      'Payment'
    ]);
    $_SESSION['ErrorState'] = '<div class="alert alert-success"><strong>Successfully Added the Charge</strong> <br>
		The user will be billed on their next billing date.</div>';
  } catch (Exception $e) {
    $_SESSION['ErrorState'] = '<div class="alert alert-danger"><strong>An error occured</strong> <br>
		The charge could not be added.</div>';
  }

} else {
	$_SESSION['ErrorState'] = '<div class="alert alert-danger"><strong>The selected user could not be found</strong> <br>
	The charge could not be added.</div>';
}

header("Location: " . autoUrl("payments/newcharge"));
