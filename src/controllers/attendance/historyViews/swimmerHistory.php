<?php

global $db;

// Get the last four weeks to calculate attendance
$sql = $db->query("SELECT `WeekID` FROM `sessionsWeek` ORDER BY `WeekDateBeginning` DESC LIMIT 1 OFFSET 20");
$earliestWeek = $sql->fetchColumn();

if ($earliestWeek == null) {
  $sql = $db->query("SELECT `WeekID` FROM `sessionsWeek` ORDER BY `WeekDateBeginning` ASC LIMIT 1");
  $earliestWeek = $sql->fetchColumn();
}

if ($earliestWeek == null) {
  // No weeks
}

$getMember = $db->prepare("SELECT MForename first, MSurname last FROM `members` WHERE `MemberID` = ?");
$getMember->execute([$id]);
$member = $getMember->fetch(PDO::FETCH_ASSOC);

if ($member == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($member['first'] . " " . $member['last']) . " Attendance History";

$getPresent = $db->prepare("SELECT * FROM (`sessionsAttendance` INNER JOIN `sessions` ON sessionsAttendance.SessionID=sessions.SessionID) WHERE WeekID >= ? AND `MemberID` = ? ORDER BY WeekID DESC, SessionDay DESC, StartTime DESC");
if ($earliestWeek != null) {
  $getPresent->execute([
    $earliestWeek,
    $id
  ]);
}

$present = $getPresent->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>

<div class="container">

  <h1>Attendance History for <?=htmlspecialchars($member['first'] . " " . $member['last'])?></h1>
  <p class="lead">
    You are now viewing attendance records for up to the last 20 weeks
  </p>

  <?php if ($getPresent == null) { ?>
    <div class="alert alert-warning">
      <p class="mb-0"><strong>No information available</strong></p>
			<p class="mb-0">This is likely because no registers have been taken at sessions this swimmer could attend.</p>
    </div>
  <?php } else { ?>

  <div class="table-responsive-md">
  	<table class="table">
  		<thead>
  			<tr><th>Session</th><th>Attendance</th></tr>
  		</thead>
  		<tbody>

        <?php do {
        	$sessionID = $present['SessionID'];
        	$weekID = $present['WeekID'];
          $details = $db->prepare("SELECT * FROM ((`sessionsAttendance` INNER JOIN sessions ON sessions.SessionID=sessionsAttendance.sessionID) INNER JOIN sessionsWeek ON sessionsWeek.WeekID=sessionsAttendance.WeekID) WHERE sessionsAttendance.SessionID = ? AND MemberID = ? AND sessionsAttendance.WeekID = ?");
          $details->execute([
            $sessionID,
            $id,
            $weekID
          ]);
        	$sessionInfo = $details->fetch(PDO::FETCH_ASSOC);

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

          ?>

          <tr>
        	  <td>
              <?=htmlspecialchars($sessionInfo['SessionName'])?>, <?=$dayText?> <?=$date?> at <?=$sessionInfo['StartTime']?>
        	    <?php if ($present['MainSequence'] != 1) { ?>
                (Not Mandatory)
              <?php } ?>
        		</td>
            <td>

            	<?php if ($present['AttendanceBoolean'] == 1) { ?>
          		<div>
          	    &#10003;
          	  </div>
            	<?php } else { ?>
          		<div class="d-print-none">

          	  </div>
            	<?php } ?>
            </td>
          </tr>
        <?php } while ($present = $getPresent->fetch(PDO::FETCH_ASSOC)); ?>
      </tbody>
    </table>
  </div>

  <?php } ?>

</div>
<?php $footer = new \SCDS\Footer();
$footer->render();
