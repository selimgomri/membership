<?php

global $db;

$name = $price = $errorMessage = null;
$errorState = false;

if ($_POST['name'] != null && $_POST['name'] != "") {
	$name =	trim($_POST['name']);
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that name</li>";
}

if ($_POST['price'] != null && $_POST['price'] != "") {
	$price = number_format($_POST['price'],2,'.','');
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that price</li>";
}

if (!$errorState) {
  try {
    $insert = $db->prepare("INSERT INTO `extras` (`ExtraName`, `ExtraFee`) VALUES (?, ?)");
    $insert->execute([$name, $price]);
    header("Location: " . autoUrl("payments/extrafees"));
  } catch (Exception $e) {
    $errorState = true;
		$errorMessage .= "<li>Unable to add to database</li>";
  }
}

if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	Something went wrong and we couldn\'t carry out that operation
	<ul class="mb-0">' . $errorMessage . '</ul></div>';
	header("Location: " . autoUrl("payments/extrafees/new"));
}
