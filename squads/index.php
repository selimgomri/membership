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
  $pagetitle = "Squads";

  if ($access == "Parent") {
    // Not allowed or not found
    $pagetitle = "Error 404 - Not found";
    $title = "Error 404 - Not found";
    $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
    include "../header.php";
  }
  elseif ($access == "Galas") {
    // Gala Access
    $pagetitle = "Squads";
    $title = "Squad Details";
    $content = "<p class=\"lead\">Information about our squads</p>";
    $content .= squadInfoTable($link);
    include "../header.php";
  }
  elseif ($access == "Coach") {
    if ($id == "") {
      $pagetitle = "Squads";
      $title = "Squad Details";
      $content = "<p class=\"lead\">Information about our squads</p>";
      $content .= squadInfoTable($link, true);
    }
    elseif ($id != null || $id != "") {
      $pagetitle = "Squads";
      $title = "Squad Details";
      $content = "<p class=\"lead\">Information about our squads</p>";
      $content .= squadInfoTable($link, true);
    }
    else {
    }
    include "../header.php";
  }
  elseif ($access == "Committee" || $access == "Admin") {
    if ($id == "") {
      $pagetitle = "Squads";
      $title = "Squad Details";
      $content = "<p class=\"lead\">Information about our squads</p>";
      $content .= squadInfoTable($link, true);
    }
    elseif (($id != null || $id != "")) {
      include "SquadIndividual.php";
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
    // Error
  }

?>
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include "../footer.php";
?>
