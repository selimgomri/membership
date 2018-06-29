<?

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM `paymentMandates` WHERE `MandateID` = '$id' AND `UserID` = '$user' AND `InUse` = '1';";
if (mysqli_num_rows(mysqli_query($link, $sql)) != 1) {
	halt(404);
}

$sql = "UPDATE `paymentPreferredMandate` SET `MandateID` = '$id' WHERE `UserID` = '$user';";
if (mysqli_query($link, $sql)) {
	header("Location: " . autoUrl("payments/mandates"));
} else {
	halt(500);
}
