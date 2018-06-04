<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];
  $header = true;

  // Requested resource
  $pos = strrpos ($URI . "users/" , '/');
  $url = mysqli_real_escape_string(LINK, $_SERVER['REQUEST_URI']);
  $url = preg_replace('{/$}', '', $url);
  //$pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);

  $pos = strrpos($url, '/');
  $idLast = $pos === false ? $url : substr($url, $pos + 1);

  function getUserNameByID($db, $id) {
    $sql = "SELECT `Forename`, `Surname` FROM `users` WHERE UserID = '$id';";
    $result = mysqli_query($db, $sql);
    if ($result) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      return $row['Forename'] . " " . $row['Surname'];
    }
  }

  function getUserInfoByID($db, $id) {
    $sql = "SELECT * FROM users WHERE UserID = '$id';";
    $outputResult = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($outputResult, MYSQLI_ASSOC);
    $grav_url = 'https://www.gravatar.com/avatar/' . md5( strtolower( trim( $row['EmailAddress'] ) ) ) . "?d=" . urlencode("https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png") . "&s=80";
    $output = '
    <div class="d-flex align-items-center p-3 my-3 text-white bg-primary rounded box-shadow" id="dash">
      <img class="mr-3" src="' . $grav_url . '" alt="" width="48" height="48">
      <div class="lh-100">
        <h6 class="mb-0 text-white lh-100">' . $row['Forename'] . ' ' . $row['Surname'] . '</h6>
        <small>' . $row['AccessLevel'] . '</small>
      </div>
    </div>
    <div class="my-3 p-3 bg-white rounded box-shadow">
      <h2 class="border-bottom border-gray pb-2 mb-0">Basic Information</h2>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Name</strong>
          ' . $row['Forename'] . ' ' . $row['Surname'] . '
        </p>
      </div>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Username</strong>
          ' . $row['Username'] . '
        </p>
      </div>
      <div class="media pt-3">
        <p class="media-body mb-0 lh-125">
          <strong class="d-block text-gray-dark">Account Type</strong>
          ' . $row['AccessLevel'] . '
        </p>
      </div>
    </div>
    <div class="my-3 p-3 bg-white rounded box-shadow">
      <h2 class="border-bottom border-gray pb-2 mb-0">Contact Details</h2>
      <div class="media pt-3">
        <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <strong class="d-block text-gray-dark">Email Address</strong>
          <a href="mailto:' . $row['EmailAddress'] . '">' . $row['EmailAddress'] . '</a>
        </p>
      </div>
      <div class="media pt-3">
        <p class="media-body mb-0 lh-125">
          <strong class="d-block text-gray-dark">Mobile Number</strong>
          ' . $row['Mobile'] . '
        </p>
      </div>
    </div>
    ';
    return $output;
  }

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
      include "../header.php";
    }

    include "../header.php";

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
      include "../header.php";
    }

    if ($header == true) {
      include "../header.php";
    }

  }
  else {
    // Error
    header("HTTP/1.1 404 Not Found");
    $pagetitle = "Error 404 - Not found";
    $title = "Error 404 - Not found";
    $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
    include "../header.php";
  }

if ($header == true) {
?>
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include "../footer.php";
}
?>
