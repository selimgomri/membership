<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];

  // Requested resource

  $pos = strrpos ($URI . "galas/" , '/');
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $url = preg_replace('{/$}', '', $url);
  //$pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);

  $pos = strrpos($url, '/');
  $idLast = $pos === false ? $url : substr($url, $pos + 1);

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
    elseif ($id == "entergala-action") {
      // Parent Page
      include "galaentriesaction.php";
    }
    elseif (($id == "entries")) {
      // Show entry info if it exists
      $pagetitle = $title = "Galas you've entered";
      $content .= enteredGalas($link, $userID);
    }
    elseif (($id == "entries/updategala-action")) {
      // Show entry info if it exists
      include "entriesSingleaction.php";
    }
    elseif (($id == "entries/" . $idLast)) {
      // Show entry info if it exists
      include "entriesSingle.php";
    }
    elseif (($id == "competitions")) {
      // Show entry info if it exists
      include "listGalas.php";
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
  elseif ($access == "Galas" || $access == "Committee" || $access == "Admin") {
    // Gala Access
    if ($id == "") {
      include "listGalas.php";
    }
    elseif ($id == "addgala") {
      include "addGala.php";
    }
    elseif ($id == "addgala-action") {
      include "addGalaAction.php";
    }
    elseif (($id == "entries")) {
      // Show entry info if it exists
      include "allEntries.php";
    }
    elseif ($id == "entries/filter/" . $idLast) {
      include "allEntries.php";
    }
    elseif (($id == "entries-action")) {
      // Show entry info if it exists
      include "allEntriesAction.php";
    }
    elseif (($id == "entries/updategala-action")) {
      // Show entry info if it exists
      include "entriesSingleaction.php";
    }
    elseif (($id == "entries/" . $idLast)) {
      // Show entry info if it exists
      include "entriesSingle.php";
    }
    elseif (($id == "competitions")) {
      // Show entry info if it exists
      include "listGalas.php";
    }
    elseif (($id == "competitions/updategala-action")) {
      // Show entry info if it exists
      include "competitionSingleaction.php";
    }
    elseif (($id == "competitions/" . $idLast)) {
      // Show entry info if it exists
      include "competitionSingle.php";
    }
    else {
    }
    include "../header.php";
  }
  elseif ($access == "Coach") {
    if ($id == "") {
      include "listGalas.php";
    }
    elseif ($id == "addgala") {
      include "addGala.php";
    }
    elseif ($id == "addgala-action") {
      include "addGalaAction.php";
    }
    elseif ($id == "entries/filter/" . $idLast) {
      include "allEntries.php";
    }
    elseif (($id == "entries")) {
      // Show entry info if it exists
      include "allEntries.php";
    }
    elseif (($id == "entries/updategala-action")) {
      // Show entry info if it exists
      $pagetitle = $title = "Oops. You can't do that";
      $content = "<p>Coaches are unable to edit gala entries. Contact a parent or committee member to do that.</p>";
    }
    elseif (($id == "entries/" . $idLast)) {
      // Show entry info if it exists
      include "entriesSingle.php";
    }
    elseif (($id == "competitions/updategala-action")) {
      // Show entry info if it exists
      include "competitionSingleaction.php";
    }
    elseif (($id == "competitions/" . $idLast)) {
      // Show entry info if it exists
      include "competitionSingle.php";
    }
    else {
    }
    include "../header.php";
  }
  else {
    // Error
    header("HTTP/1.1 404 Not Found");
    $pagetitle = "Error 404 - Not found";
    $title = "Error 404 - Not found";
    $content = '<p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. You may also not be authorised to view the page.</p>';
    include "../header.php";
  }

?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
    <a class="nav-link" href="<?php echo autoUrl("galas")?>">Gala Home</a>
    <a class="nav-link" href="<?php echo autoUrl("galas/competitions")?>">Competitions</a>
    <?php if ($access == "Parent") {?>
    <a class="nav-link" href="<?php echo autoUrl("galas/entries")?>">My Entries</a>
    <?php } else {?>
    <a class="nav-link" href="<?php echo autoUrl("galas/entries")?>">View Entries</a>
    <?php } ?>
    <a class="nav-link" href="https://www.chesterlestreetasc.co.uk/competitions/" target="_blank">Go to Gala Website</a>
  </nav>
</div>
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include "../footer.php";
?>
