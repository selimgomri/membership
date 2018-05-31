<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];

  // Requested resource
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);
  $id = (int)($id);

  // Variables for display
  $title = $content = '';
  $pagetitle = "Squads";

  if ($access == "Committee" || $access == "Admin") {
    if ($id == "") {
      $pagetitle = "Squads";
      $title = "Squad Details";
      $content = "<p class=\"lead\">Information about our squads</p>";
      $content .= squadInfoTable($link, true);
      $content .= "<p><a href=\"addsquad\" class=\"btn btn-outline-dark\">Add a Squad</a></p>";
    }
    elseif (($id == "addsquad")) {
      include "SquadAdd.php";
    }
    elseif (($id == "addsquad-action")) {
      include "SquadAddAction.php";
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
    if ($id == "") {
      $pagetitle = "Squads";
      $title = "Squad Details";
      $content = "<p class=\"lead\">Information about our squads</p>";
      $content .= squadInfoTable($link, true);
    }
    elseif ($id != null || $id != "") {
      include "SquadIndividual.php";
    }
    else {
    }
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
