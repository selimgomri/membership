<?php

$id = $idLast;

$sql = "SELECT * FROM (`sessions` INNER JOIN sessionsVenues ON sessions.VenueID = sessionsVenues.VenueID) WHERE `SessionID` = '$id';";
$result = mysqli_query(LINK, $sql);
$sessionCount = mysqli_num_rows($result);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

if ($sessionCount == 1) {

	$dayText = "";
	switch ($row['SessionDay']) {
			case 0:
					$dayText = "Sunday";
					break;
			case 1:
					$dayText = "Monday";
					break;
			case 2:
					$dayText = "Tuesday";
					break;
			case 3:
					$dayText = "Wednesday";
					break;
			case 4:
					$dayText = "Thursday";
					break;
			case 5:
					$dayText = "Friday";
					break;
			case 6:
					$dayText = "Saturday";
					break;
	}

	$pagetitle = $row['SessionName'];
	$title = $row['SessionName'];
	$content = '<p class="lead">' . $dayText . ' at ' . $row['StartTime'] . '</p>
	<dl>
		<dt>Session Name</dt>
		<dd>' . $row['SessionName'] . '</dd>
		<dt>Main Sequence</dt>
		<dd>' . $row['MainSequence'] . '<br>
		<em>If 0, sessions will be ignored from attendance calculations. Use this for extra sessions which only some swimmers in a squad are to attend</em></dd>
		<dt>Venue</dt>
		<dd>' . $row['VenueName'] . '</dd>
		<dt>Start Time</dt>
		<dd>' . $row['StartTime'] . '</dd>
		<dt>Finish Time</dt>
		<dd>' . $row['EndTime'] . '</dd>
		<dt>Session Duration</dt>';
		$datetime1 = new DateTime($row['StartTime']);
		$datetime2 = new DateTime($row['EndTime']);
		$interval = $datetime1->diff($datetime2);
		$content .= '
		<dd>' . $interval->format('%h hours %I minutes') . '</dd>
		<div id="successAlert"></div>
		<dt><label for="endDate">Display Until</label></dt>
		<dd><input type="date" class="form-control mb-3" id="endDate" name="endDate" value="';
		if ($row['DisplayUntil'] != null) {
			$content .= $row['DisplayUntil'];
		}
		$content .= '">
		<button id="saveDate" class="btn btn-outline-dark">Save End Date</button></dd>
	</dl>
	<p><strong>You can\'t edit a session once it has been created</strong>  <br>Sessions are immutable. This is because swimmers may be marked as present at a session in the past, changing the session in any way, such as altering the start or finish time would distort the attendance records. Instead, set a DisplayUntil date for the session, after which it will not appear in the register, but will still be visible in attendance history</p>
	<script>
	var save = document.getElementById("saveDate");
	save.addEventListener("click", setDate, false)

	function setDate(clickedItem, clickedItemChecked) {
		var date = document.getElementById(\'endDate\');
		dateValue = date.value;
	  var xhttp = new XMLHttpRequest();
	  xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	      document.getElementById("successAlert").innerHTML = \'<div class="alert alert-success"><strong>We have saved the new end date</strong> <br>This session will disappear from the register after this date, but still display in past attendance records</div>\';
	    }
			else {
				document.getElementById("successAlert").innerHTML = \'<div class="alert alert-danger"><strong>We failed to update that properly</strong> <br>Please reload the page and try again</div>\';
			}
	  };
	  xhttp.open("POST", "' . autoUrl("ajax/sessionsEndDate.php") . '", true);
	  console.log("POST", "' . autoUrl("ajax/sessionsEndDate.php") . '", true);
	  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	  xhttp.send("sessionID=' . $row['SessionID'] . '&sessionEndDate=" + dateValue);
	  console.log("sessionID=' . $row['SessionID'] . '&sessionEndDate=" + dateValue);
	  console.log("Sent");
	}
	</script>';
}
else {
	//break;
	echo "BROKE";
}
