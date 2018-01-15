<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];

  // Requested resource
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);

  // Variables for display
  $title = $content = '';

  if ($id == "") {
    $pagetitle = "My Swimmers";
    $title = "My Swimmers";
    $content = "<p class=\"lead\">My Swimmers shows you all of your registered swimmers and allows you to easily change their details.</p>";
    $content .= "<p>Please remember that it is your responsibility to also keep the ASA Membership System up to date with personal details.</p>";
    $content .= mySwimmersTable($link, $userID);
    include "../header.php";
  }
  elseif (($id != null || $id != "")) {
    // Get the swimmer name
    $sqlSecurityCheck = "SELECT `MForename`, `MSurname`, `UserID` FROM `members` WHERE MemberID = '$id';";
    $resultSecurityCheck = mysqli_query($link, $sqlSecurityCheck);
    $swimmersSecurityCheck = mysqli_fetch_array($resultSecurityCheck, MYSQLI_ASSOC);

    $pagetitle;
    if ($swimmersSecurityCheck['UserID'] == $userID && $resultSecurityCheck) {
      $pagetitle = "Swimmer: " . $swimmersSecurityCheck['MForename'] . " " . $swimmersSecurityCheck['MSurname'];
      $sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames, members.MSurname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
      $resultSwim = mysqli_query($link, $sqlSwim);
      $swimmersSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
      $title = $swimmersSecurityCheck['MForename'] . " " . $swimmersSecurityCheck['MSurname'];
      $content = "<div class=\"row\"><div class=\"col col-md-8\">";
      // Main Info Content
      $content .= "<p>Date of Birth: " . $swimmersSwim['DateOfBirth'] . "</p>";
      $test = $swimmersSwim['DateOfBirth'];
      $test = date('j M Y', strtotime($test));
      $content .= "<p>Date of Birth: " . $test . "</p>";
      $content .= "</div><div class=\"col-md-4\">";
      $content .= "<div class=\"cell\"><h2>Squad Information</h2><ul class=\"mb-0\"><li>Squad: " . $swimmersSwim['SquadName'] . "</li><li>Monthly Fee: &pound;" . $swimmersSwim['SquadFee'] . "</li><li><a href=\"" . $swimmersSwim['SquadTimetable'] . "\">Squad Timetable</a></li><li><a href=\"" . $swimmersSwim['SquadCoC'] . "\">Squad Code of Conduct</a></li></ul></div>";
      $content .= "</div></div>";
    }
    else {
      // Not allowed or not found
      $pagetitle = "Error 404 - Not found";
      $title = "Error 404 - Not found";
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
