<?php
include_once "../database.php";
$access = $_SESSION['AccessLevel'];
$count = 0;

// A function is used to produce the View/Edit and Add Sections Stuff
// This is because we will call it when a squad is selected, and after a session is added

function sessionManagement($squadID, $link) {
	$output = $content = $modals = "";

	$content .= '
	<div class="row">
	<div class="col-md-6">

	<div class="my-3 p-3 bg-white rounded box-shadow">
	<h2 class="border-bottom border-gray pb-2 mb-0">View/Edit Sessions</h2>
	';

	$sql = "SELECT * FROM (`sessions` INNER JOIN sessionsVenues ON sessions.VenueID = sessionsVenues.VenueID) WHERE `SquadID` = '$squadID' ORDER BY `SessionDay` ASC, `StartTime` ASC;";
	$result = mysqli_query($link, $sql);
	$count = mysqli_num_rows($result);
	if ($count > 0) {
		for ($i=0; $i<$count; $i++) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

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

			$content .= '
			<div class="media text-muted pt-3">
		    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
		      <a data-toggle="modal" href="#sessionModal' . $row['SessionID'] . '"><strong class="d-block text-gray-dark">' . $row['SessionName'] . ', ' . $dayText . ' at ' . $row['StartTime'] . '</strong></a>
		      ' . $row['VenueName'] . '
		    </p>
		  </div>
			';
			$modals .= '
			<!-- Modal -->
			<div class="modal fade" id="sessionModal' . $row['SessionID'] . '" tabindex="-1" role="dialog" aria-labelledby="sessionModalTitle' . $row['SessionID'] . '" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="sessionModalTitle' . $row['SessionID'] . '">Session ' . $row['SessionName'] . ', ' . $dayText . '</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<form id="sessionEditModal' . $row['SessionID'] . '">
							<div class="form-group">
								<label for="sessionEditName' . $row['SessionID'] . '">Session Name</label>
								<input type="text" class="form-control" id="sessionEditName' . $row['SessionID'] . '" name="sessionEditName' . $row['SessionID'] . '" value="' . $row['SessionName'] . '">
							</div>
							<div class="form-group">
								<label for="sessionEditDay' . $row['SessionID'] . '">Day of Week</label>
								<input type="text" class="form-control" id="sessionEditDay' . $row['SessionID'] . '" name="sessionEditDay' . $row['SessionID'] . '" value="' . $dayText . '">
							</div>
						</form>
						</div>
					</div>
				</div>
			</div>';
		}
	}
	else {
		$content .= '<p class="pt-3 mb-0">Oops. There aren\'t any sessions for this squad yet. Try adding one</p>';
	}

	$content .= '
	</div>
	</div>

	<div class="col-md-6">

	<div class="my-3 p-3 bg-white rounded box-shadow">
	<h2 class="border-bottom border-gray pb-2">Add Session</h2>

		<div class="form-group">
			<label for="newSessionName">Session Name</label>
			<input type="text" class="form-control" name="newSessionName" id="newSessionName" placeholder="Name">
		</div>
		<div class="form-group">
			<label for="newSessionDay">Session Day</label>
			<select class="custom-select" name="newSessionDay" id="newSessionDay">
				<option value="9" selected>Select a Day</option>
				<option value="0">Sunday</option>
				<option value="1">Monday</option>
				<option value="2">Tuesday</option>
				<option value="3">Wednesday</option>
				<option value="4">Thursday</option>
				<option value="5">Friday</option>
				<option value="6">Saturday</option>
			</select>
		</div>
		<div class="form-group">
			<label for="newSessionVenue">Session Venue</label>
			<select class="custom-select" name="newSessionVenue" id="newSessionVenue">
				<option selected value="0">Select a Venue</option>';
				$sql = "SELECT * FROM `sessionsVenues`;";
				$result = mysqli_query($link, $sql);
				$count = mysqli_num_rows($result);
				if ($count>0) {
					for ($i=0; $i<$count; $i++) {
						$venuesRow = mysqli_fetch_array($result, MYSQLI_ASSOC);
						$content .= '<option value="' . $venuesRow['VenueID'] . '">' . $venuesRow['VenueName'] . '</option>';
					}
				}
				$content .= '

			</select>
		</div>
		<div class="form-group">
			<label for="newSessionStartTime">Start Time</label>
			<input type="time" class="form-control" name="newSessionStartTime" id="newSessionStartTime" placeholder="0">
			<small id="newSessionStartTimeHelp" class="form-text text-muted">Make sure to use 24 Hour Time</small>
		</div>
		<div class="form-group">
			<label for="newSessionEndTime">End Time</label>
			<input type="time" class="form-control" name="newSessionEndTime" id="newSessionEndTime" placeholder="0">
			<small id="newSessionEndTimeHelp" class="form-text text-muted">Make sure to use 24 Hour Time</small>
		</div>
		<p class="mb-0"><button class="btn btn-outline-dark" id="newSessionAction" onclick="addSession();">Add Session</button></p>

	</div>
	</div>
	</div>

';

	return $content . $modals;

}

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
  $sql = "";
  if (isset($_POST["action"])) {
		// Get the action to work out what we're going to do
		$action = mysqli_real_escape_string($link, htmlentities($_POST["action"]));
		if ($action == "getSessions") {
	    // get the squadID parameter from post
			$squadID = "";
			if (isset($_POST["squadID"])) {
	    	$squadID = mysqli_real_escape_string($link, htmlentities($_POST["squadID"]));
				echo sessionManagement($squadID, $link);
			}

			$sql = "";
		}
		elseif ($action == "addSession") {
			$squadID = $venueID = $sessionName = $sessionDay = $startTime = $endTime = "";
			if ((isset($_POST["squadID"])) && (isset($_POST["venueID"])) && (isset($_POST["sessionName"])) && (isset($_POST["sessionDay"])) && (isset($_POST["startTime"])) && (isset($_POST["endTime"]))) {
	    	$squadID = mysqli_real_escape_string($link, htmlentities($_POST["squadID"]));
				$venueID = mysqli_real_escape_string($link, htmlentities($_POST["venueID"]));
				$sessionName = mysqli_real_escape_string($link, htmlentities($_POST["sessionName"]));
				$sessionDay = mysqli_real_escape_string($link, htmlentities($_POST["sessionDay"]));
				$startTime = mysqli_real_escape_string($link, htmlentities($_POST["startTime"]));
				$endTime = mysqli_real_escape_string($link, htmlentities($_POST["endTime"]));

				$sql = "INSERT INTO `sessions` (`SquadID`, `VenueID`, `SessionName`, `SessionDay`, `StartTime`, `EndTime`, `SessionActive`) VALUES ('$squadID', '$venueID', '$sessionName', '$sessionDay', '$startTime', '$endTime', '1');";
				mysqli_query($link, $sql);

				echo sessionManagement($squadID, $link);

			}
		}
	}
}
else {
	echo "BROKE";
}
