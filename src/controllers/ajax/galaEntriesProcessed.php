<?php
$access = $_SESSION['AccessLevel'];
if ($access == "Committee" || $access == "Admin" || $access == "Coach" || $access == "Galas") {
	if ((isset($_POST["processedID"])) && (isset($_POST["clickedItemChecked"])) && (isset($_POST["verify"]))) {

		// get the galaID parameter from POST
    $id = mysqli_real_escape_string($link, htmlspecialchars($_POST["processedID"]));
		$itemChecked = mysqli_real_escape_string($link, htmlspecialchars($_POST["clickedItemChecked"]));
		$verify = mysqli_real_escape_string($link, htmlspecialchars($_POST["verify"]));

	if ($verify == "markProcessed" /*&& $itemChecked != null*/) {
			if (strpos($id, 'processedEntry-') !== false) {
			    // Okay it's what we want. Lets remove the "processedEntry" so we just have the ID left over
					$id = substr($id, 15);
					$sql = "";
					if ($itemChecked == "true") {
						$sql = "UPDATE `galaEntries` SET `EntryProcessed` = '1' WHERE `EntryID` = '$id'";
					}
					else {
						$sql = "UPDATE `galaEntries` SET `EntryProcessed` = '0' WHERE `EntryID` = '$id'";
					}
					mysqli_query($link, $sql);
			}
		}
  }
}
?>
