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
  $pagetitle = "Galas";

  if ($access == "Parent") {
    if ($id == "") {
      include "parentHome.php";
    }
    elseif ($id == "entergala") {
      // Parent Page
      include "galaentries.php";
    }
    elseif (($id != "")) {
      // Show entry info if it exists
      include "parentSingle.php";
    }
    else {
      // Not allowed or not found
      $pagetitle = "Error 404 - Not found";
      $title = "Error 404 - Not found";
      $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
      $content .= "<p>" . $id . "</p>";
      $content .= "<p>Hello</p>";
    }
    include "../header.php";
  }
  elseif ($access == "Galas") {
    // Gala Access
    if ($id == "") {
      include "listGalas.php";
    }
    elseif (($id != null || $id != "")) {
      include "Entries.php";
    }
    else {
    }
    include "../header.php";
  }
  elseif ($access == "Coach") {
    if ($id == "") {
    }
    elseif (($id != null || $id != "")) {
    }
    else {
    }
    include "../header.php";
  }
  elseif ($access == "Committee" || $access == "Admin") {
    if ($id == "") {
    }
    elseif (($id != null || $id != "")) {
    }
    else {
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
