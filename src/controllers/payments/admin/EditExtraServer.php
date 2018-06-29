<?php

$id = mysqli_real_escape_string($link, $id);

$name = $price = $errorMessage = null;
$errorState = false;

if ($_POST['name'] != null && $_POST['name'] != "") {
	$name =	mysqli_real_escape_string($link, $_POST['name']);
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that name</li>";
}

if ($_POST['price'] != null && $_POST['price'] != "") {
	$price = mysqli_real_escape_string($link, number_format($_POST['price'],2,'.',''));
} else {
	$errorState = true;
	$errorMessage .= "<li>There was a problem with that price</li>";
}

if (!$errorState) {
	$sql = "UPDATE `extras` SET `ExtraName` = '$name', `ExtraFee` = '$price' WHERE `ExtraID` = '$id';";
	if (!mysqli_query($link, $sql)) {
		$errorState = true;
		$errorMessage .= "<li>Unable to edit item in database</li>";
	} else {
		header("Location: " . autoUrl("payments/extrafees/" . $id));
	}
}

if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	Something went wrong and we couldn\'t carry out that operation
	<ul class="mb-0">' . $errorMessage . '</ul></div>';
	header("Location: " . autoUrl("payments/extrafees/" . $id . "/edit"));
}
