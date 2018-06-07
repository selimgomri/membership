<?php
$access = $_SESSION['AccessLevel'];
?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
    <a class="nav-link" href="<?php echo autoUrl("attendance")?>">Attendance Home</a>
    <?php if ($access == "Parent") {?>
    <a class="nav-link" href="<?php echo autoUrl("attendance/history")?>">Attendance History</a>
    <?php } else {?>
    <a class="nav-link" href="<?php echo autoUrl("attendance/register")?>">Take Register</a>
    <?php if (($access == "Admin") || ($access == "Committee")) {?>
    <a class="nav-link" href="<?php echo autoUrl("attendance/sessions")?>">Manage Sessions</a>
    <?php }?>
    <a class="nav-link" href="<?php echo autoUrl("attendance/history")?>">Attendance History</a>
    <?php } ?>
    <a class="nav-link" href="https://www.chesterlestreetasc.co.uk/squads/" target="_blank">Timetables</a>
  </nav>
</div>
