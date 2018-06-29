<?php
$id = mysqli_real_escape_string($link, $id);
$newAccess = mysqli_real_escape_string($link, $_POST['accountType']);

if ($newAccess != "Admin" && $newAccess != "Committee" && $newAccess != "Coach" && $newAccess != "Galas" && $newAccess != "Parent") {
	$newAccess = "Parent";
}

$sql = "UPDATE `users` SET `AccessLevel` = '$newAccess' WHERE `UserID` = '$id';";
if (mysqli_query($link, $sql)) {
	?><span class="text-success pt-3">Updated Account Type Successfully</span><?php
} else {
	?><span class="text-danger mt-2">Failed to update account type</span><?php
}
?>
