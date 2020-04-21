<?php

$db = app()->db;

$access = $_SESSION['AccessLevel'];
if ($access == "Committee" || $access == "Admin") {
	if ((isset($_POST["sessionID"])) && (isset($_POST["sessionEndDate"]))) {

		// get the galaID parameter from POST
    $id = $_POST["sessionID"];
		$endDate = date("Y-m-d", strtotime($_POST["sessionEndDate"]));

		if ($id != null) {
			$update = $db->prepare("UPDATE `sessions` SET `DisplayUntil` = ? WHERE `SessionID` = ?");
			$update->execute([$endDate, $id]);
		}
	}
}
?>
