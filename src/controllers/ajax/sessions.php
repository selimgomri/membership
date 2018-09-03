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

	<div class="my-3 p-3 bg-white rounded shadow">
	<h2 class="border-bottom border-gray pb-2 mb-0">View Sessions</h2>
	';

	$sql = "SELECT * FROM (`sessions` INNER JOIN sessionsVenues ON sessions.VenueID = sessionsVenues.VenueID) WHERE `SquadID` = '$squadID' AND (ISNULL(sessions.DisplayFrom) OR (sessions.DisplayFrom <= CURDATE( ))) AND (ISNULL(sessions.DisplayUntil) OR (sessions.DisplayUntil >= CURDATE( ))) ORDER BY `SessionDay` ASC, `StartTime` ASC;";
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
							<h5 class="modal-title" id="sessionModalTitle' . $row['SessionID'] . '">' . $row['SessionName'] . ', ' . $dayText . ' at ' . $row['StartTime'] . '</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
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
					      $modals .= '
								<dd>' . $interval->format('%h hours %I minutes') . '</dd>
								<dt>Display Until</dt>
								<dd>';
								if ($row['DisplayUntil'] != null) {
									$modals .= date('j F Y', strtotime($row['DisplayUntil']));
								}
								else {
									$modals .= "Not set";
								}
								$modals .= '
								<a class="btn btn-outline-dark" href="sessions/' . $row['SessionID'] . '">Edit End Date</a></dd>
							</dl>
							<strong>You can\'t edit a session once it has been created</strong>  <br>Sessions are immutable. This is because swimmers may be marked as present at a session in the past, changing the session in any way, such as altering the start or finish time would distort the attendance records. Instead, set a DisplayUntil date for the session, after which it will not appear in the register, but will still be visible in attendance history
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

	<div class="my-3 p-3 bg-white rounded shadow">
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
			<label for="newSessionMS">Main Sequence</label>
			<div class="custom-control custom-radio">
			  <input type="radio" id="newSessionMSYes" name="newSessionMS" class="custom-control-input" value="1">
			  <label class="custom-control-label" for="newSessionMSYes">Yes, the session is for the full squad</label>
			</div>
			<div class="custom-control custom-radio">
			  <input type="radio" id="newSessionMSNo" name="newSessionMS" class="custom-control-input" value="0">
			  <label class="custom-control-label" for="newSessionMSNo">	No, this session is only for selected swimmers</label>
			</div>
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
		<div class="form-group">
			<label for="newSessionStartDate">Show From</label>
			<input type="date" aria-labelledby="newSessionStartDateHelp" class="form-control" name="newSessionStartDate" id="newSessionStartDate" placeholder="0">
			<small id="newSessionStartDateHelp" class="form-text text-muted">The date from which the session will appear in the registers. To show from today, just leave it blank</small>
		</div>
		<div class="form-group">
			<label for="newSessionEndDate">Show Until</label>
			<input type="date" aria-labelledby="newSessionStartDateHelp" class="form-control" name="newSessionEndDate" id="newSessionEndDate" placeholder="0">
			<small id="newSessionEndDateHelp" class="form-text text-muted">If you know when this session will stop running, enter the last date here</small>
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
			if ((isset($_POST["squadID"])) && (isset($_POST["venueID"])) && (isset($_POST["sessionName"])) && (isset($_POST["sessionDay"])) && (isset($_POST["startTime"])) && (isset($_POST["endTime"])) && (isset($_POST["newSessionMS"])) && (isset($_POST["newSessionStartDate"])) && (isset($_POST["newSessionEndDate"]))) {
	    	$squadID = mysqli_real_escape_string($link, htmlentities($_POST["squadID"]));
				$venueID = mysqli_real_escape_string($link, htmlentities($_POST["venueID"]));
				$sessionName = mysqli_real_escape_string($link, htmlentities($_POST["sessionName"]));
				$sessionDay = mysqli_real_escape_string($link, htmlentities($_POST["sessionDay"]));
				$startTime = mysqli_real_escape_string($link, htmlentities($_POST["startTime"]));
				$endTime = mysqli_real_escape_string($link, htmlentities($_POST["endTime"]));
				$mainSequence = mysqli_real_escape_string($link, htmlentities($_POST["newSessionMS"]));
				$epoch = date(DATE_ATOM, mktime(0, 0, 0, 1, 1, 1970));
				$future = date(DATE_ATOM, mktime(0, 0, 0, 1, 1, 2200));
				$displayFrom = $displayUntil = null;
				if (isset($_POST["newSessionStartDate"])) {
					$displayFrom = strtotime(mysqli_real_escape_string($link, htmlentities($_POST["newSessionStartDate"])));
					if ($displayFrom < $epoch) {
						$displayFrom = null;
					}
				}
				if (isset($_POST["newSessionEndDate"])) {
					$displayUntil = strtotime(mysqli_real_escape_string($link, htmlentities($_POST["newSessionEndDate"])));
					if ($displayUntil < $epoch) {
						$displayUntil = $future;
					}
				}

				$sql = "INSERT INTO `sessions` (`SquadID`, `VenueID`, `SessionName`, `SessionDay`, `StartTime`, `EndTime`, `MainSequence`, `DisplayFrom`, `DisplayUntil`) VALUES ('$squadID', '$venueID', '$sessionName', '$sessionDay', '$startTime', '$endTime', '$mainSequence', '$displayFrom', '$displayUntil');";
				mysqli_query($link, $sql);

				echo sessionManagement($squadID, $link);

			}
		}
	}
}
else {
	echo "BROKE";
}
