<?php

global $db;

$sql = $db->prepare("SELECT * FROM (`sessions` INNER JOIN sessionsVenues ON sessions.VenueID = sessionsVenues.VenueID) WHERE `SessionID` = ?;");
$sql->execute([$id]);
$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
	halt(404);
}

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

$pagetitle = htmlspecialchars($row['SessionName']);

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php";

?>


<div class="container">
  <h1><?=htmlspecialchars($row['SessionName'])?></h1>
	<p class="lead"><?=$dayText?> at <?=$row['StartTime']?></p>
	<dl>
		<dt>Session Name</dt>
		<dd><?=htmlspecialchars($row['SessionName'])?></dd>
		<dt>Main Sequence</dt>
		<dd><?=htmlspecialchars($row['MainSequence'])?><br>
		<em>If 0, sessions will be ignored from attendance calculations. Use this for extra sessions which only some swimmers in a squad are to attend</em></dd>
		<dt>Venue</dt>
		<dd><?=htmlspecialchars($row['VenueName'])?></dd>
		<dt>Start Time</dt>
		<dd><?=htmlspecialchars($row['StartTime'])?></dd>
		<dt>Finish Time</dt>
		<dd><?=htmlspecialchars($row['EndTime'])?></dd>
		<dt>Session Duration</dt>
		<?php
		
		$datetime1 = new DateTime($row['StartTime']);
		$datetime2 = new DateTime($row['EndTime']);
		$interval = $datetime1->diff($datetime2);

		?>
		<dd><?=htmlspecialchars($interval->format('%h hours %I minutes'))?></dd>
		<div id="successAlert"></div>
		<dt><label for="endDate">Display Until</label></dt>
		<dd><input type="date" class="form-control mb-3" id="endDate" name="endDate" value="<?php if ($row['DisplayUntil'] != null) { ?><?=htmlspecialchars($row['DisplayUntil'])?><?php } ?>" data-session-id="<?=$row['SessionID']?>">
		<button id="saveDate" class="btn btn-outline-dark">Save End Date</button></dd>
	</dl>
	<p><strong>You can't edit a session once it has been created</strong>  <br>Sessions are immutable. This is because swimmers may be marked as present at a session in the past, changing the session in any way, such as altering the start or finish time would distort the attendance records. Instead, set a DisplayUntil date for the session, after which it will not appear in the register, but will still be visible in attendance history</p>
</div>

<?php $footer = new \SCDS\Footer();
$footer->addJs("js/attendance/edit-end-date.js");
$footer->render();
