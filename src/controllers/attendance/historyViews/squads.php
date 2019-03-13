<?php

$sql = "SELECT * FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC;";
$result = mysqli_query($link, $sql);

$pagetitle = "Attendance History by Squad";
$title = "Attendance History by Squad";
$content = "<p class=\"lead\">View Attendance History for a squad</p>";

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>
<div class="container">
	<div class="mb-3 p-3 bg-white rounded shadow">
		<h1>Attendance History</h1>
		<p class="lead border-bottom border-gray pb-2">View Attendance History for a squad</p>

		<ul class="mb-0">
			<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				?><li>
					<a href="<?php echo autoUrl("attendance/history/squads/" . $row['SquadID']); ?>">
						<?php echo $row['SquadName']; ?> Squad
					</a>
				</li>
			<?php } ?>
		</ul>

	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
