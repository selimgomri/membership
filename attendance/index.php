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
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include "../footer.php";
?>
