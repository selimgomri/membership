<?php

$id = mysqli_real_escape_string($link, $id);

// Get the last four weeks to calculate attendance
$sql = "SELECT `WeekID` FROM `sessionsWeek` ORDER BY `WeekDateBeginning` DESC LIMIT 20;";
$resultWeeks = mysqli_query($link, $sql);
$weekCount = mysqli_num_rows($resultWeeks);
	if ($weekCount > 0) {
	$sqlWeeks = "";
	// Produce stuff for query
	for ($y=0; $y<$weekCount; $y++) {
		$attRow = mysqli_fetch_array($resultWeeks, MYSQLI_ASSOC);
		$weekID[$y] = $attRow['WeekID'];
		if ($y < ($weekCount-1)) {
			$sqlWeeks .= "`WeekID` = '$weekID[$y]' OR ";
		}
		else {
			$sqlWeeks .= "`WeekID` = '$weekID[$y]'";
		}
	}
}

$sql = "SELECT * FROM `members` WHERE `MemberID` = '$id';";
$result = mysqli_query($link, $sql);
$memberCount = mysqli_num_rows($result);
$member = mysqli_fetch_array($result, MYSQLI_ASSOC);

$pagetitle = $member['MForename'] . " " . $member['MSurname'] . " Attendance History";
$title = "Attendance History for " . $member['MForename'] . " " . $member['MSurname'];
$content = "<p class=\"lead\">You are now viewing attendance records for up to the last 20 weeks</p>";

$sql = "SELECT * FROM (`sessionsAttendance` INNER JOIN `sessions` ON sessionsAttendance.SessionID=sessions.SessionID) WHERE ($sqlWeeks) AND `MemberID` = '$id' ORDER BY WeekID DESC, SessionDay DESC, StartTime DESC;";
$resultAtt = mysqli_query($link, $sql);
$presentCount = mysqli_num_rows($resultAtt);

$content .= '
<div class="table-md-responsive">
	<table class="table">
		<thead>
			<tr><th>Session</th><th>Attendance</th></tr>
		</thead>
		<tbody>';

for ($i=0; $i<$presentCount; $i++) {
	$att = mysqli_fetch_array($resultAtt, MYSQLI_ASSOC);
	$sessionID = $att['SessionID'];
	$weekID = $att['WeekID'];
	$content .= '<tr>';
	$sql = "SELECT * FROM ((`sessionsAttendance` INNER JOIN sessions ON sessions.SessionID=sessionsAttendance.sessionID) INNER JOIN sessionsWeek ON sessionsWeek.WeekID=sessionsAttendance.WeekID) WHERE sessionsAttendance.SessionID = '$sessionID' AND MemberID = '$id' AND sessionsAttendance.WeekID = '$weekID';";
	$resultSession = mysqli_query($link, $sql);
	$sessionInfo = mysqli_fetch_array($resultSession, MYSQLI_ASSOC);

	$weekBeginning = $sessionInfo['WeekDateBeginning'];
	$dayAdd = $sessionInfo['SessionDay'];
	$date = date ('j F Y', strtotime($weekBeginning. ' + ' . $dayAdd . ' days'));

	$dayText = "";
	switch ($sessionInfo['SessionDay']) {
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

	$content .= "<td>" . $sessionInfo['SessionName'] . ", " . $dayText . " " . $date . " at " . $sessionInfo['StartTime'];
	if ($att['MainSequence'] != 1) {
		$content .= '
		 (Not Mandatory)
		';
	}
	$content .= "</td><td>";

	if ($att['AttendanceBoolean'] == 1) {
		$content .= '
		<div>
	    &#10003;
	  </div>
		';
	}
	else {
		$content .= '
		<div class="d-print-none">

	  </div>
		';
	}

	$content .=  "</td>";

	$content .= '</tr>';
}

$content .= '
		</tbody>
	</table>
</div>';

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
