<?php

$pagetitle = "Attendance History";
$title = "Attendance History";
$content = '
<div class="my-3 p-3 bg-white rounded box-shadow">
  <h2>View Options</h2>
  <p class="border-bottom border-gray pb-2 mb-0">You\'ll be able to see attendance history here for squads soon. In the mean time, you can view attendance through <a href="./register">the Register</a> by selecting Week, Squad and Session</p>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
      <a href="history/swimmers"><strong class="d-block text-gray-dark">Swimmer Attendance</strong></a>
      View attendance records for up to the last 20 weeks
    </p>
  </div>
  <div class="media pt-3">
    <p class="media-body pb-3 mb-0 lh-125">
      <a href="history/squads"><strong class="d-block">Squad Attendance</strong></a>
      View attendance for previous sessions across full squads
    </p>
  </div>
</div>
';

include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/attendance/attendanceMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
