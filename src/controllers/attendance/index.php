<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];

  $pos = strrpos ($URI . "attendance/" , '/');
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $url = preg_replace('{/$}', '', $url);
  //$pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);

  $pos = strrpos($url, '/');
  $idLast = $pos === false ? $url : substr($url, $pos + 1);

  // Variables for display
  $title = "Service closed for maintenance";
  $content = '<p>We\'ll be back shortly. Please wait</p>';
  $pagetitle = "Attendance";

  if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
    if ($id == "") {
      include "indexView.php";
    }
    elseif (($id == "register")) {
      $pagetitle = "Register";
      $title = "Register";
      $content = "<p class=\"lead\">Take the register for your Squad</p>";
      include "register.php";
    }
    elseif (($id == "register.post")) {
      $pagetitle = "";
      $title = "";
      $content = "";
      include "POST/register.php";
    }
    elseif (($id == "sessions")) {
      $pagetitle = "Add or Edit Sessions";
      $title = "Add or Edit Sessions";
      $content = "<p class=\"lead\">Every squad has sessions $linked to it. These are required for our attendance application. The data about sessions is also used to provide information to parents (in future).</p>";
      include "sessions.php";
    }
    elseif (($id == "sessions/" . $idLast)) {
      include "sessionViews/editEndDate.php";
    }
    elseif (($id == "history")) {
      include "historyViews/history.php";
    }
    elseif (($id == "history/squads")) {
      include "historyViews/squads.php";
    }
    elseif (($id == "history/squads/" . $idLast)) {
      $pagetitle = "Attendance History for SquadID " . $idLast;
      $title = "Attendance History for SquadID " . $idLast;
      $content = "<p class=\"lead\"> " . $idLast . "</p>";
      include "historyViews/squadHistory.php";
    }
    elseif (($id == "history/swimmers")) {
      include "historyViews/swimmers.php";
    }
    elseif ($id == "history/swimmers/filter/" . $idLast) {
      include "historyViews/swimmers.php";
    }
    elseif (($id == "history/swimmers/" . $idLast)) {
      include "historyViews/swimmerHistory.php";
    }
    include BASE_PATH . "views/header.php";
  }
  else {
    // Not allowed or not found
    header("HTTP/1.1 404 Not Found");
    $pagetitle = "Error 404 - Not found";
    $title = "Error 404 - Not found";
    $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
    include BASE_PATH . "views/header.php";
  }

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
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include BASE_PATH . "views/footer.php";
?>
