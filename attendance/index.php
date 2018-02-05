<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];

  // Requested resource
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);

  // Variables for display
  $title = $content = '';
  $pagetitle = "Attendance";

  if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
    if ($id == "") {
      $pagetitle = "Attendance";
      $title = "Squad Attendance <span class=\"badge badge-secondary\" style=\"background:var(--pink)\">ALPHA</span>";
      $content = "<p class=\"lead\">A demo to test the data structure for recording attendance</p>";
      $content .= "<a class=\"btn btn-dark mb-2\" href=\"register\">Take Register</a>";
    }
    elseif (($id == "register")) {
      $pagetitle = "Register";
      $title = "Register <span class=\"badge badge-secondary\" style=\"background:var(--pink)\">ALPHA</span>";
      $content = "<p class=\"lead\">Take the register for your Squad</p>";
      include "register.php";
    }
    elseif (($id == "sessions")) {
      $pagetitle = "Add or Edit Sessions";
      $title = "Add or Edit Sessions";
      $content = "<p class=\"lead\">Every squad has sessions linked to it. These are required for our attendance application. The data about sessions is also used to provide information to parents (in future).</p>";
      include "sessions.php";
    }
    elseif (($id == "register.post")) {
      $pagetitle = "";
      $title = "";
      $content = "";
      include "POST/register.php";
    }
    else {
      // Argh. Something went wrong
      $pagetitle = "Error";
      $title = "Error 500";
      $content = "<p class=\"lead\">We couldn't do anything.</p>";
    }
    include "../header.php";
  }
  else {
    // Not allowed or not found
    header("HTTP/1.1 404 Not Found");
    $pagetitle = "Error 404 - Not found";
    $title = "Error 404 - Not found";
    $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
    include "../header.php";
  }

?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
    <a class="nav-link" href="<?php echo autoUrl("attendance")?>">Attendance Home</a>
    <?php if ($access == "Parent") {?>
    <a class="nav-link" href="<?php echo autoUrl("attendance/history")?>">Attendance History</a>
    <?php } else {?>
    <a class="nav-link" href="<?php echo autoUrl("attendance/register")?>">Take Register</a>
    <a class="nav-link" href="<?php echo autoUrl("attendance/sessions")?>">Manage Sessions</a>
    <a class="nav-link" href="<?php echo autoUrl("attendance/history")?>">Attendance History</a>
    <?php } ?>
    <a class="nav-link" href="https://www.chesterlestreetasc.co.uk/squads/" target="_blank">Timetables</a>
  </nav>
</div>
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include "../footer.php";
?>
