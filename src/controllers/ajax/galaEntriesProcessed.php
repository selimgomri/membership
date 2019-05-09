<?php

global $db;
$update = $db->prepare("UPDATE galaEntries SET EntryProcessed = ? WHERE EntryID = ?");
$markPaid = $db->prepare("UPDATE galaEntries SET Charged = ? WHERE EntryID = ?");

$access = $_SESSION['AccessLevel'];
if ($access == "Committee" || $access == "Admin" || $access == "Coach" || $access == "Galas") {
	if ((isset($_POST["processedID"])) && (isset($_POST["clickedItemChecked"])) && (isset($_POST["verify"]))) {

		// get the galaID parameter from POST
    $id = $_POST["processedID"];
		$itemChecked = $_POST["clickedItemChecked"];
		$verify = $_POST["verify"];

  	if ($verify == "markProcessed" /*&& $itemChecked != null*/) {
  		if (strpos($id, 'processedEntry-') !== false) {
  	    // Okay it's what we want. Lets remove the "processedEntry" so we just have the ID left over
  			$id = substr($id, 15);
  			if ($itemChecked == "true") {
  				$update->execute([true, $id]);
  			}
  			else {
  				$update->execute([true, $id]);
  			}
  		}
  	} else if ($verify == "markPaid") {
      if (strpos($id, 'chargedEntry-') !== false) {
  	    // Okay it's what we want. Lets remove the "chargedEntry" so we just have the ID left over
  			$id = substr($id, 13);
  			if ($itemChecked == "true") {
  				$markPaid->execute([true, $id]);
  			}
  			else {
  				$markPaid->execute([true, $id]);
  			}
  		}
    }
  }
}
?>
