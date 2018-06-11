<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];
  $header = true;

  // Requested resource
  $pos = strrpos ($URI . "users/" , '/');
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $url = preg_replace('{/$}', '', $url);
  //$pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);

  $pos = strrpos($url, '/');
  $idLast = $pos === false ? $url : substr($url, $pos + 1);

  // Variables for display
  $title = $content = '';

  if ($access == "Galas") {
    // Gala Access
    if ($id == "") {
      include "userDirectory.php";
    }
    elseif ($id == "filter/" . $idLast) {
      include "userDirectory.php";
    }
    elseif (($id != null || $id != "")) {
      include "user.php";
    }
    else {
      // Not allowed or not found
      header("HTTP/1.1 404 Not Found");
      $pagetitle = "Error 404 - Not found";
      $title = "Error 404 - Not found";
      $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
      include BASE_PATH . "views/header.php";
    }

    include BASE_PATH . "views/header.php";

  }
  elseif ($access == "Committee" || $access == "Admin") {
    if ($id == "") {
      include "userDirectory.php";
    }
    elseif ($id == "filter/" . $idLast) {
      include "userDirectory.php";
    }
    elseif (($id == "edit/" . $idLast)) {
      include "singleSwimmerEdit.php";
      }
    elseif (($id != null || $id != "")) {
      include "user.php";
    }
    else {
      // Not allowed or not found
      header("HTTP/1.1 404 Not Found");
      $pagetitle = "Error 404 - Not found";
      $title = "Error 404 - Not found";
      $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
      include BASE_PATH . "views/header.php";
    }

    if ($header == true) {
      include BASE_PATH . "views/header.php";
    }

  }
  else {
    // Error
    header("HTTP/1.1 404 Not Found");
    $pagetitle = "Error 404 - Not found";
    $title = "Error 404 - Not found";
    $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
    include BASE_PATH . "views/header.php";
  }

if ($header == true) {
?>
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include BASE_PATH . "views/footer.php";
}
?>
