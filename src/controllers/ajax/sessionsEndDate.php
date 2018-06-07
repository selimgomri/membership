<?php

$access = $_SESSION['AccessLevel'];
if ($access == "Committee" || $access == "Admin") {
	if ((isset($_POST["sessionID"])) && (isset($_POST["sessionEndDate"]))) {

		// get the galaID parameter from POST
    $id = mysqli_real_escape_string($link, htmlspecialchars($_POST["sessionID"]));
		$endDate = mysqli_real_escape_string($link, htmlspecialchars($_POST["sessionEndDate"]));

		if ($id != null) {
			$sql = "UPDATE `sessions` SET `DisplayUntil` = '$endDate' WHERE `SessionID` = '$id'";
			mysqli_query($link, $sql);
		}
	}
}
?>
