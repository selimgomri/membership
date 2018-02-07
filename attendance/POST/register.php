<?php
$duplicateReg = false;
if ((isset($_POST["date"])) && (isset($_POST["squad"])) && (isset($_POST["session"]))) {
	// Happy Days. Now we just need the members

	$weekBeginningID = mysqli_real_escape_string($link, htmlentities($_POST["date"]));
	$squadID = mysqli_real_escape_string($link, htmlentities($_POST["squad"]));
	$sessionID = mysqli_real_escape_string($link, htmlentities($_POST["session"]));

	// SQL to check we've not done the register before
	$sql = "SELECT * FROM `sessionsAttendance` WHERE `WeekID` = '$weekBeginningID' AND `SessionID` = '$sessionID';";
	$result = mysqli_query($link, $sql);
	$registerCount = mysqli_num_rows($result);

	// SQL to get the member IDs
	$sqlMembers = $sql = "SELECT `MemberID` FROM `members` WHERE `SquadID` = '$squadID';";
	$result = mysqli_query($link, $sql);
	$swimmerCount = mysqli_num_rows($result);

	// Initialise the attendance values string for MySQL
	$values = "";

	// Insert the register data
	if ($registerCount < 1) {
		for ($i=0; $i<$swimmerCount; $i++) {
			$attendance = 0;
			// Get the attendance value, if it is set for each member
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			if (isset($_POST["Member-" . $row['MemberID']])) {
				$attendance = 1;
			}
			if ($i<($swimmerCount-1)) {
				$values .= "(" . $weekBeginningID . "," . $row['MemberID'] . "," . $sessionID . "," . $attendance . "),";
			}
			else {
				$values .= "(" . $weekBeginningID . "," . $row['MemberID'] . "," . $sessionID . "," . $attendance . ")";
			}
		}

		$sql = "INSERT INTO `sessionsAttendance` (WeekID, MemberID, SessionID, AttendanceBoolean) VALUES " . $values . ";";
		if (mysqli_query($link, $sql)) {
			// Return info page
			$return = "<strong>Successfully saved the session register</strong> <br>For more information, contact <a href=\"mailto:mms@chesterlestreetasc.co.uk\" class=\"alert-link\">mms@chesterlestreetasc.co.uk</a>";
			$_SESSION['return'] = $return;
		}
	}
	else {
		$result = mysqli_query($link, $sqlMembers);
		for ($i=0; $i<$swimmerCount; $i++) {
			$attendance = 0;
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			if (isset($_POST["Member-" . $row['MemberID']])) {
				$attendance = 1;
			}
			$sql = "UPDATE `sessionsAttendance` SET AttendanceBoolean = '$attendance' WHERE MemberID = " . $row['MemberID'] . " AND WeekID = '$weekBeginningID' AND SessionID = '$sessionID';";
			mysqli_query($link, $sql);
				// Return info page
				//$return = "<strong>Successfully saved the session register</strong> <br>For more information, contact <a href=\"mailto:mms@chesterlestreetasc.co.uk\" class=\"alert-link\">mms@chesterlestreetasc.co.uk</a>";
				//$_SESSION['return'] = $return;
		}
		$duplicateReg = true;
	}

	if ($duplicateReg == true) {
		$return = "<p><strong>Updated</strong></p>";
		$_SESSION['return'] = $return;
	}

}
else {
	$return = "<p><strong>An Error Occurred</strong> <br>For more information, contact <a href=\"mailto:mms@chesterlestreetasc.co.uk\" class=\"alert-link\">mms@chesterlestreetasc.co.uk</a></p>";
	$_SESSION['return'] = $return;
}
header("Location: " . autoUrl("attendance/register"));
