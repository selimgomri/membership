<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];
  $header = true;

  // Requested resource
  $pos = strrpos ($URI . "swimmers/" , '/');
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $url = preg_replace('{/$}', '', $url);
  //$pos = strrpos($url, '/');
  $id = mysqli_real_escape_string($link, $pos === false ? $url : substr($url, $pos + 1));

  $pos = strrpos($url, '/');
  $idLast = mysqli_real_escape_string($link, $pos === false ? $url : substr($url, $pos + 1));

  function getMemberNameByID($db, $id) {
    $sql = "SELECT `MForename`, `MSurname` FROM `members` WHERE MemberID = '$id';";
    $result = mysqli_query($db, $sql);
    if ($result) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      return $row['MForename'] . " " . $row['MSurname'];
    }
  }

  function getMemberInfoByID($db, $id) {
    $sql = "SELECT `UserID` FROM `members` WHERE MemberID = '$id';";
    $prelimResult = mysqli_query($db, $sql);
    $prelimRow = mysqli_fetch_array($prelimResult, MYSQLI_ASSOC);
    if ($prelimRow['UserID'] != null || $prelimRow['UserID'] != "") {
      $sql = "SELECT members.MForename, members.MMiddleNames, members.MSurname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadCoach, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
    }
    else {
      $sql = "SELECT members.MForename, members.MMiddleNames, members.MSurname, members.ASANumber, squads.SquadName, squads.SquadCoach, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
    }
    $outputResult = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($outputResult, MYSQLI_ASSOC);
    $output = "<div class=\"table-responsive\"><table class=\"table table-hover\">";
    $output .= "<thead><tr><th>Column</th><th>Field</th></tr></thead><tbody>";
    // Main Info Content
    $output .= "<tr><th>Forename</th><td>" . $row['MForename'] . "</td></tr>";
    if ($row['MMiddleNames'] != null) {
      $output .= "<tr><th>Middle Names</th><td>" . $row['MMiddleNames'] . "</td></tr>";
    }
    $output .= "<tr><th>Surname</th><td>" . $row['MSurname'] . "</td></tr>";
    $output .= "<tr><th>Date of Birth</th><td>" . date('j F Y', strtotime($row['DateOfBirth'])) . "</td></tr>";
    $output .= "<tr><th>ASA Number</th><td><a href=\"https://www.swimmingresults.org/biogs/biogs_details.php?tiref=" . $row['ASANumber'] . "\" target=\"_blank\" title=\"ASA Biographical Data\">" . $row['ASANumber'] . "</a></td></tr>";
    $output .= "<tr><th>Sex</th><td>" . $row['Gender'] . "</td></tr>";
    if ($row['MedicalNotes'] != null) {
      $output .= "<tr><th>Medial Notes</th><td>" . $row['MedicalNotes'] . "</td></tr>";
    }
    if ($row['OtherNotes'] != null) {
      $output .= "<tr><th>Notes</th><td>" . $row['OtherNotes'] . "</td></tr>";
    }
    $output .= "<tr><th>Squad</th><td>" . $row['SquadName'] . "</td></tr>";
    $output .= "<tr><th>Coach</th><td>" . $row['SquadCoach'] . "</td></tr>";
    $output .= "</tbody></table></div>";
    return $output;
  }

  // Variables for display
  $title = $content = '';

  if ($access == "Parent") {
    if ($id == "") {
      $pagetitle = "My Swimmers";
      $title = "My Swimmers";
      $content = "<p class=\"lead\">My Swimmers shows you all of your registered swimmers and allows you to easily change their details.</p>";
      $content .= "<p>Please remember that it is your responsibility to also keep the ASA Membership System up to date with personal details.</p>";
      $content .= mySwimmersTable($link, $userID);
      $content .= "<p><a href=\"" . autoUrl('myaccount/add-swimmer.php') . "\" class=\"btn btn-outline-dark\">Add a Swimmer</a></p>";

      include "../header.php";
    }
    elseif (($id == "edit/" . $idLast)) {
      include "parentSingleSwimmer.php";
    }
    elseif (($id != null || $id != "")) {
      include "parentSingleSwimmerView.php";
    }
  }
  elseif ($access == "Galas") {
    // Gala Access
    if ($id == "") {
      include "swimmerDirectory.php";
    }
    elseif ($id == "filter/" . $idLast) {
      include "swimmerDirectory.php";
    }
    elseif (($id != null || $id != "")) {
      // Get the swimmer function
      include "singleSwimmerView.php";
      //$pagetitle = "Swimmer: " . getMemberNameByID($link, $id);
      //$title = getMemberNameByID($link, $id);
      //$content = getMemberInfoByID($link, $id);
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
  elseif ($access == "Coach") {
    // Coaches access details about their squads
    if ($id == "") {
      include "swimmerDirectory.php";
    }
    elseif ($id == "filter/" . $idLast) {
      include "swimmerDirectory.php";
    }
    elseif (($id == "edit/" . $idLast)) {
      include "parentSingleSwimmer.php";
    }
    elseif (($id != null || $id != "")) {
      include "singleSwimmerView.php";
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
      include "swimmerDirectory.php";
    }
    elseif ($id == "filter/" . $idLast) {
      include "swimmerDirectory.php";
    }
    elseif ($id == "accesskeys") {
      $pagetitle = "Access Keys";
      $title = "Member Access Keys";
      $content = "<p class=\"lead\">See access keys.</p>";
      $content .= "<p><a href=\"accesskeys-csv\" class=\"btn btn-outline-dark\">Download as a CSV for Mailmerge</a></p>";
      include "accesskeys.php";
    }
    elseif ($id == "accesskeys-csv") {
      $header = false;
      include_once "../database.php";
      $pagetitle = "Access Keys as a CSV";
      $title = "Member Access Keys as a CSV";
      $content = "<p class=\"lead\">Great for a mailmerge</p>";
      include "accesskeysCSV.php";
    }
    elseif ($id == "add-member") {
      $pagetitle = "Add a member";
      $title = "Add a member";
      $content = "<p class=\"lead\">Add a member to the club system.</p>";
      $added = false;

      $forename = $middlenames = $surname = $dateOfBirth = $asaNumber = $sex = $squad = $sql = "";

      if ((!empty($_POST['forename']))  && (!empty($_POST['surname'])) && (!empty($_POST['datebirth'])) && (!empty($_POST['sex'])) && (!empty($_POST['squad']))) {
        $forename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
        $surname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
        $dateOfBirth = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['datebirth'])));
        $sex = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['sex'])));
        $squad = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['squad'])));
        if ((!empty($_POST['middlenames']))) {
          $middlenames = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['middlenames']))));
        }
        if ((!empty($_POST['asa']))) {
          $asaNumber = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['asa'])));
        }

        $accessKey = generateRandomString(6);

        $sql = "INSERT INTO `members` (`MemberID`, `MForename`, `MMiddleNames`, `MSurname`, `DateOfBirth`, `ASANumber`, `Gender`, `SquadID`, `AccessKey`) VALUES (NULL, '$forename', '$middlenames', '$surname', '$dateOfBirth', '$asaNumber', '$sex', '$squad', '$accessKey');";
        $action = mysqli_query($link, $sql);
        if ($action) {
          $added = true;
        }
      }

      $content = "<div class=\"row\"><div class=\"col col-md-8\">";
      if ($added) {
      $content .= '<div class="alert alert-success">
        <strong>We added the member</strong>';
      $content .= '</div>';
      }
      // Main Info Content
      $content .= "<form method=\"post\">";
      $content .= "
      <div class=\"form-group\">
        <label for=\"forename\">Forename</label>
        <input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"middlenames\">Middle Names</label>
        <input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\">
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"surname\">Surname</label>
        <input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"datebirth\">Date of Birth</label>
        <input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" placeholder=\"YYYY-MM-DD\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"asa\">ASA Registration Number</label>
        <input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\" placeholder=\"ASA Registration Numer\">
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"sex\">Sex</label>
        <select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
          <option value=\"Male\">Male</option>
          <option value=\"Female\">Female</option>
        </select>
      </div>";
      $sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
      $result = mysqli_query($link, $sql);
      $squadCount = mysqli_num_rows($result);
      $content .= "
      <div class=\"form-group\">
        <label for=\"squad\">Squad</label>
          <select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
      //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      for ($i = 0; $i < $squadCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "<option value=\"" . $row['SquadID'] . "\"";
        $content .= ">" . $row['SquadName'] . "</option>";
      }
      $content .= "</select></div>";
      $content .= "<button type=\"submit\" class=\"btn btn-outline-dark mb-3\">Add Member</button>";
      $content .= "</div><div class=\"col-md-4\">";
      $content .= "</div></div>";
    }

    elseif (($id == "edit/" . $idLast)) {
      include "singleSwimmerEdit.php";
      }
    elseif (($id != null || $id != "")) {
      include "singleSwimmerView.php";
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
