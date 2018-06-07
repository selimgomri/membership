<?php
$id = mysqli_real_escape_string($link, $id);
$pagetitle = "Attendance History for SquadID " . $id;
$title = "Attendance History for SquadID " . $id;
$content = "<p class=\"lead\"> " . $id . "</p>";
include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
?>
