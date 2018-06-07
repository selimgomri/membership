<?php
$pagetitle = "Attendance History by Squad";
$title = "Attendance History by Squad";
$content = "<p class=\"lead\">View Attendance History for a squad</p>";

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
