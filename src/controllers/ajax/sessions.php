<?php

if (!isset($_POST["action"])) {
	halt(404);
}

global $db;

$access = $_SESSION['AccessLevel'];
$count = 0;

// A function is used to produce the View/Edit and Add Sections Stuff
// This is because we will call it when a squad is selected, and after a session is added

function sessionManagement($squadID, $old = null) {
  global $db;
	$output = $content = $modals = "";
	
	$getSessions = $db->prepare("SELECT * FROM (`sessions` INNER JOIN sessionsVenues ON sessions.VenueID = sessionsVenues.VenueID) WHERE `SquadID` = ? AND (ISNULL(sessions.DisplayFrom) OR (sessions.DisplayFrom <= CURDATE( ))) AND (ISNULL(sessions.DisplayUntil) OR (sessions.DisplayUntil >= CURDATE( ))) ORDER BY `SessionDay` ASC, `StartTime` ASC");
	$getSessions->execute([$squadID]);

	$venues = $db->query("SELECT VenueName `name`, VenueID id FROM `sessionsVenues` ORDER BY VenueName ASC");

	$row = $getSessions->fetch(PDO::FETCH_ASSOC);
	
	?>

<div class="row">
  <div class="col-md-6">
    <div class="card mb-3">
			<div class="card-body">
				<h2 class="card-title">Sessions</h2>
      <?php if ($row != null) { ?>
				<p class="card-text">
					Sessions are ordered by day of week and time
				</p>
			</div>
			<ul class="list-group list-group-flush">
			<?php
		do {

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
			} ?>
      <li class="list-group-item">
        <p class="mb-0">
          <a data-toggle="modal" href="#sessionModal<?=$row['SessionID']?>">
            <strong class="text-gray-dark">
              <?=htmlspecialchars($row['SessionName'])?>, <?=$dayText?> at <?=$row['StartTime']?>
            </strong>
          </a>
        </p>
        <p class="mb-0"><?=htmlspecialchars($row['VenueName'])?></p>
      </li>

      <?php 
			$modals .= '
			<!-- Modal -->
			<div class="modal fade" id="sessionModal' . $row['SessionID'] . '" tabindex="-1" role="dialog" aria-labelledby="sessionModalTitle' . $row['SessionID'] . '" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="sessionModalTitle' . $row['SessionID'] . '">' . htmlspecialchars($row['SessionName']) . ', ' . $dayText . ' at ' . $row['StartTime'] . '</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<dl>
								<dt>Session Name</dt>
								<dd>' . htmlspecialchars($row['SessionName']) . '</dd>
								<dt>Include in attendance calculations</dt>';
								if ($row['MainSequence']) {
									$modals .= '<dd>This session is included in attendance calculations</dd>';
								} else {
									$modals .= '<dd>This session is <strong>not included</strong> in attendance calculations</dd>';
								}
								$modals .= '<dt>Venue</dt>
								<dd>' . htmlspecialchars($row['VenueName']) . '</dd>
								<dt>Start Time</dt>
								<dd>' . htmlspecialchars($row['StartTime']) . '</dd>
								<dt>Finish Time</dt>
								<dd>' . htmlspecialchars($row['EndTime']) . '</dd>
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
								<a class="btn btn-dark" href="sessions/' . $row['SessionID'] . '">Edit End Date</a></dd>
							</dl>
							<strong>You can\'t edit a session once it has been created</strong>  <br>Sessions are immutable. This is because swimmers may be marked as present at a session in the past, changing the session in any way, such as altering the start or finish time would distort the attendance records. Instead, set a DisplayUntil date for the session, after which it will not appear in the register, but will still be visible in attendance history
						</div>
					</div>
				</div>
			</div>';
		} while ($row = $getSessions->fetch(PDO::FETCH_ASSOC)); ?>
		</ul>
		<?php
	}
	else { ?>
				<div class="alert alert-warning mb-0">
					There aren't any sessions for this squad yet. Try adding one
				</div>
				</div>
				<?php } ?>
    </div>
  </div>

  <div class="col-md-6">

		<div class="card mb-3">
			<div class="card-body">
				<h2>Add Session</h2>

				<div class="form-group">
					<label for="newSessionName">Session Name</label>
					<input type="text" class="form-control" name="newSessionName" id="newSessionName" placeholder="Name">
					<small id="newSessionStartDateHelp" class="form-text text-muted">
						e.g. <em>Swimming</em>, <em>Land Training</em>
					</small>
				</div>
				<div class="form-row">
					<div class="col">
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
					</div>
					<div class="col">
						<div class="form-group">
							<label for="newSessionVenue">Session Venue</label>
							<select class="custom-select" name="newSessionVenue" id="newSessionVenue">
								<option selected value="0">Select a Venue</option>
								<?php while ($venue = $venues->fetch(PDO::FETCH_ASSOC)) { ?>
								<option value="<?=$venue['id']?>">
									<?=htmlspecialchars($venue['name'])?>
								</option>
								<?php } ?>

							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="newSessionMS">Include in attendance count</label>
					<div class="custom-control custom-radio">
						<input type="radio" id="newSessionMSYes" name="newSessionMS" class="custom-control-input" value="1">
						<label class="custom-control-label" for="newSessionMSYes">
							Yes, the session is for the full squad
						</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="newSessionMSNo" name="newSessionMS" class="custom-control-input" value="0">
						<label class="custom-control-label" for="newSessionMSNo">
							No, this session is only for selected swimmers
						</label>
					</div>
				</div>
				<div class="form-row">
					<div class="col">
						<div class="form-group">
							<label for="newSessionStartTime">Start Time</label>
							<input type="time" class="form-control" name="newSessionStartTime" id="newSessionStartTime" placeholder="0" value="18:00">
							<small id="newSessionStartTimeHelp" class="form-text text-muted">
								Make sure to use 24 Hour Time
							</small>
						</div>
					</div>
					<div class="col">
						<div class="form-group">
							<label for="newSessionEndTime">End Time</label>
							<input type="time" class="form-control" name="newSessionEndTime" id="newSessionEndTime" placeholder="0" value="18:30">
							<small id="newSessionEndTimeHelp" class="form-text text-muted">
								Make sure to use 24 Hour Time
							</small>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div class="col">
						<div class="form-group">
							<label for="newSessionStartDate">Show From</label>
							<input type="date" aria-labelledby="newSessionStartDateHelp" class="form-control" name="newSessionStartDate"
								id="newSessionStartDate" placeholder="0" value="<?=date("Y-m-d")?>">
							<small id="newSessionStartDateHelp" class="form-text text-muted">
								The date from which the session will appear in the registers
							</small>
						</div>
					</div>
					<div class="col">
						<div class="form-group">
							<label for="newSessionEndDate">Show Until</label>
							<input type="date" aria-labelledby="newSessionStartDateHelp" class="form-control" name="newSessionEndDate"
								id="newSessionEndDate" placeholder="0">
							<small id="newSessionEndDateHelp" class="form-text text-muted">
								If you know when this session will stop running, enter the last date here
							</small>
						</div>
					</div>
				</div>
				<p class="mb-0"><button class="btn btn-success" id="newSessionAction" onclick="addSession();">Add Session</button>
				</p>

			</div>
		</div>
  </div>
</div>

<?php return $content . $modals;

}

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
	// Get the action to work out what we're going to do
	$action = $_POST["action"];
	if ($action == "getSessions" && $_POST["squadID"] != null) {
		echo sessionManagement($_POST["squadID"]);
} elseif ($action == "addSession") {
	$squadID = $_POST["squadID"];
	$venueID = $_POST["venueID"];
	$sessionName = $_POST["sessionName"];
	$sessionDay = $_POST["sessionDay"];
	$startTime = $_POST["startTime"];
	$endTime = $_POST["endTime"];
	$mainSequence = $_POST["newSessionMS"];
	$epoch = date(DATE_ATOM, mktime(0, 0, 0, 1, 1, 1970));
	$future = date(DATE_ATOM, mktime(0, 0, 0, 1, 1, 2200));
	$displayFrom = $displayUntil = null;
	if (isset($_POST["newSessionStartDate"])) {
		$displayFrom = strtotime($_POST["newSessionStartDate"]);
		if ($displayFrom < $epoch) {
			$displayFrom = null;
		}
	}
	if (isset($_POST["newSessionEndDate"])) {
		$displayUntil = strtotime($_POST["newSessionEndDate"]);
		if ($displayUntil < $epoch) {
			$displayUntil = $future;
		}
	}

	try {
		$insert = $db->prepare("INSERT INTO `sessions` (`SquadID`, `VenueID`, `SessionName`, `SessionDay`, `StartTime`, `EndTime`, `MainSequence`, `DisplayFrom`, `DisplayUntil`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$insert->execute([
			$squadID,
			$venueID,
			$sessionName,
			$sessionDay,
			$startTime,
			$endTime,
			$mainSequence,
			date("Y-m-d", $displayFrom),
			date("Y-m-d", $displayUntil)
		]);
	} catch (Exception $e) {
		halt(500);
	}
	echo sessionManagement($squadID);

	}
} else {
	halt(500);
}