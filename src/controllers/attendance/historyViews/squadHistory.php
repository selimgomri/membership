<?php
$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM `sessionsWeek` ORDER BY `WeekDateBeginning` DESC LIMIT 1;";
$result = mysqli_query($link, $sql);
$week = (int) mysqli_fetch_array($result, MYSQLI_ASSOC)['WeekID'];

$sql = "SELECT * FROM `squads` WHERE `SquadID` = '$id';";
$result = mysqli_query($link, $sql);
$squadName = mysqli_fetch_array($result, MYSQLI_ASSOC)['SquadName'];

$sql = "SELECT * FROM ((`members` LEFT JOIN `sessionsAttendance` ON
`sessionsAttendance`.`MemberID` = `members`.`MemberID`) INNER JOIN `sessions` ON
`sessionsAttendance`.`SessionID` = `sessions`.`SessionID`) WHERE
`sessions`.`SquadID` = '$id' AND `WeekID` = '$week' ORDER BY `MForename` ASC,
`MSurname` ASC, `SessionDay` ASC, `StartTime` ASC;";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);

if ($count == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$swimmerOld = null;

$pagetitle = "Attendance History for " . $squadName;
include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>
<div class="container">
	<div class="mb-3 p-3 bg-white rounded shadow">

		<h1>Attendance History for <? echo $squadName; ?></h1>
		<p class="lead">Squad History currently only shows the current week</p>

		<div class="table-responsive">
			<table class="table table-sm">
				<thead class="thead-light">
					<tr>
						<th>
							Swimmer
						</th>
						<th>
							Sessions
						</th>
					</tr>
				</thead>
				<tbody>
					<? for ($i = 0; $i < $count; $i++) {
						$outputname = false;
						$swimmer = $row['MemberID'];
						if ($swimmer != $swimmerOld) {
							$outputname = true;
						}
						if ($outputname) {
							?>
							<tr>
								<td>
									<? echo $row['MForename'] . " " . $row['MSurname']; ?>
								</td>
								<td>
									<ul class="list-unstyled mb-0">
							<?
						}

						$dayAdd = $row['SessionDay'];
			      $date = date ('j F Y', strtotime($weekBeginning. ' + ' . $dayAdd . ' days'));

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

						$datetime1 = new DateTime($row['StartTime']);
						$title = $row['SessionName'] . " on " . $dayText . " " . $date . " at " . $datetime1->format("H:i");

						?>
						<li>
						<? if ($row['AttendanceBoolean']) { ?>
							<? echo $title; ?>
						<? } ?>
						</li>
						<?

						$swimmerOld = $row['MemberID'];

						if ($i < $count) {
							$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
						}

						if ($i == $count-1) {
							$swimmer = null;
						}

						$swimmer = $row['MemberID'];
						if ($swimmer != $swimmerOld) {
							?>
								</ul>
							</td>
						</tr><?
						}
					}	?>
				</tbody>
			</table>
		</div>
	</div>

</div>
<?php include BASE_PATH . "views/footer.php";
?>
