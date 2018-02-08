<?php

session_start([
    'cookie_lifetime' => 86400,
]);

include_once("config.php");

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if ((!isset($preventLoginRedirect)) && (empty($_SESSION['LoggedIn']))) {
  $preventLoginRedirect = false;
  $_SESSION['requestedURL'] = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
}
elseif (!isset($preventLoginRedirect)) {
  $preventLoginRedirect = false;
  $_SESSION['requestedURL'] = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
}


require_once 'includes/PHPMailer/Exception.php';
require_once 'includes/PHPMailer/PHPMailer.php';
require_once 'includes/PHPMailer/SMTP.php';

$path = 'includes/PHPMailer';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function notifySend($to, $subject, $message) {
  // PHP Email

  // Always set content-type when sending HTML email
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= 'From: Chester-le-Street ASC <noreply@chesterlestreetasc.co.uk>' . "\r\n";
  $message = "<img src=\"https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterLogo.png\" style=\"width:300px;max-width:100%;\">" . $message . "<p>This email was sent automatically by the Chester-le-Street ASC Membership System.</p>";

  mail($to,$subject,$message,$headers);
}

function getAttendanceByID($db, $id, $weeks = "all") {
  $output = $weeksWHERE = "";
  if ($weeks != "all") {
    $number = "LIMIT $weeks";
  }

  // Get the last four weeks to calculate attendance
  $sql = "SELECT `WeekID` FROM `sessionsWeek` ORDER BY `WeekDateBeginning` DESC;";
  $resultWeeks = mysqli_query($db, $sql);
  $weekCount = mysqli_num_rows($resultWeeks);
  if ($weekCount > 0) {
    $sqlWeeks = "";
    // Produce stuff for query
    for ($y=0; $y<$weekCount; $y++) {
      $attRow = mysqli_fetch_array($resultWeeks, MYSQLI_ASSOC);
      $weekID[$y] = $attRow['WeekID'];
      if ($y < ($weekCount-1)) {
        $sqlWeeks .= "`WeekID` = '$weekID[$y]' OR ";
      }
      else {
        $sqlWeeks .= "`WeekID` = '$weekID[$y]'";
      }
    }
  }

  if ($weeks != "all") {
    $weeksWHERE = "($sqlWeeks) AND";
  }

  if ($weekCount > 0) {

    // Get number of sessions we were present at
    $sql = "SELECT `AttendanceBoolean` FROM (`sessionsAttendance` INNER JOIN `sessions` on sessionsAttendance.SessionID=sessions.SessionID) WHERE `AttendanceBoolean` = '1' AND $weeksWHERE `MemberID` = '$id' AND MainSequence = '1';";
    $resultAtt = mysqli_query($db, $sql);
    $presentCount = mysqli_num_rows($resultAtt);

    // Get number of sessions in total
    $sql = "SELECT `AttendanceBoolean` FROM (`sessionsAttendance` INNER JOIN `sessions` on sessionsAttendance.SessionID=sessions.SessionID) WHERE $weeksWHERE `MemberID` = '$id' AND MainSequence = '1';";
    $resultAtt = mysqli_query($db, $sql);
    $totalCount = mysqli_num_rows($resultAtt);

    $attPercent = 0;
    if ($totalCount != 0) {
      $attPercent = ($presentCount/$totalCount)*100;
      $output = number_format($attPercent,1,'.','');
      if ($output == 0.0) {
        $output = 0;
      }
    }
    else {
      $output = 0;
    }
  }
  else {
    $output = 0;
  }
  return $output;
}

function mySwimmersTable($db, $userID) {
  // Get the last four weeks to calculate attendance
  $sql = "SELECT `WeekID` FROM `sessionsWeek` ORDER BY `WeekDateBeginning` DESC LIMIT 4;";
  $resultWeeks = mysqli_query($db, $sql);
  $weekCount = mysqli_num_rows($resultWeeks);
    if ($weekCount > 0) {
    $sqlWeeks = "";
    // Produce stuff for query
    for ($y=0; $y<$weekCount; $y++) {
      $attRow = mysqli_fetch_array($resultWeeks, MYSQLI_ASSOC);
      $weekID[$y] = $attRow['WeekID'];
      if ($y < ($weekCount-1)) {
        $sqlWeeks .= "`WeekID` = '$weekID[$y]' OR ";
      }
      else {
        $sqlWeeks .= "`WeekID` = '$weekID[$y]'";
      }
    }
  }

  // Get the information about the swimmer
  $sqlSwim = "SELECT members.MemberID, members.MForename, members.MSurname, users.Forename, users.Surname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID = '$userID';";
  $result = mysqli_query($db, $sqlSwim);
  $swimmerCount = mysqli_num_rows($result);
  $output = "";
  if ($swimmerCount > 0) {
    $output = '
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Squad</th>
            <th>Fee</th>
            <th>ASA Number</th>
            <th><abbr title="Approximate attendance over the last 4 weeks">Attendance</abbr></th>
          </tr>
        </thead>
        <tbody>';
    $resultX = mysqli_query($db, $sqlSwim);
    for ($i = 0; $i < $swimmerCount; $i++) {
      $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
      $swimmerLink = autoUrl("swimmers/" . $swimmersRowX['MemberID'] . "");
      $output .= "<tr>
        <td><a href=\"" . $swimmerLink . "\">" . $swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname'] . "</a></td>
        <td>" . $swimmersRowX['SquadName'] . "</td>
        <td>&pound;" . $swimmersRowX['SquadFee'] . "</td>
        <td><a href=\"https://www.swimmingresults.org/biogs/biogs_details.php?tiref=" . $swimmersRowX['ASANumber'] . "\" target=\"_blank\" title=\"ASA Biographical Data\">" . $swimmersRowX['ASANumber'] . " <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a></td>";

        // Get member ID for finding attendance
        $id = $swimmersRowX['MemberID'];

        $output .= "
        <td>" . getAttendanceByID($db, $id, 4) . "%</td>
      </tr>";
    }
    $output .= '
        </tbody>
      </table>
    </div>';
  }
  return $output;
}

function mySwimmersMedia($db, $userID) {
  $sqlSwim = "SELECT members.MemberID, members.MForename, members.MSurname, users.Forename, users.Surname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID = '$userID';";
  $result = mysqli_query($db, $sqlSwim);
  $swimmerCount = mysqli_num_rows($result);
  $swimmerS = $swimmers = '';
  if ($swimmerCount == 0 || $swimmerCount > 1) {
    $swimmerS = 'swimmers';
  }
  else {
    $swimmerS = 'swimmer';
  }
  $swimmers = '<p class="lead border-bottom border-gray pb-2 mb-0">You have ' . $swimmerCount . ' ' . $swimmerS . '</p>';
  if ($swimmerCount == 0) {
    $swimmers .= '<p><a href="' . autoUrl("myaccount/add-swimmer.php") . '" class="btn btn-outline-dark">Add a Swimmer</a></p>';
  }
  $output = "";
  if ($swimmerCount > 0) {
    $output = '
    <div class="my-3 p-3 bg-white rounded box-shadow">
    <h2>My Swimmers</h2>' . $swimmers;
    $resultX = mysqli_query($db, $sqlSwim);
    for ($i = 0; $i < $swimmerCount; $i++) {
      $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
      $swimmerLink = autoUrl("swimmers/" . $swimmersRowX['MemberID'] . "");
      $output .= "<div class=\"media text-muted pt-3\"><p class=\"media-body pb-3 mb-0 lh-125 border-bottom border-gray\"><strong class=\"d-block text-gray-dark\"><a href=\"" . $swimmerLink . "\">" . $swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname'] . "</a></strong>
        " . $swimmersRowX['SquadName'] . " Squad, &pound;" . $swimmersRowX['SquadFee'] . ", " . getAttendanceByID($db, $swimmersRowX['MemberID'], 4) . "% <abbr title=\"Attendance over the last four weeks\">Attendance</abbr>
    </div>";
    }
    $output .= '
    <span class="d-block text-right mt-3">
          <a href="' . autoUrl('swimmers') . '">Go to My Swimmers</a>
        </span></div>';
  }
  else {
    $output .= '
    <div class="my-3 p-3 bg-white rounded box-shadow">
    <h2>My Swimmers</h2>
    <p class="mb-0">It looks like you have no swimmers connected to your account. Why don\'t you <a href="' . autoUrl("myaccount/add-swimmer.php") . '" >add one now</a>?</p>
    </div>';
  }
  return $output;
}

function generateRandomString($length) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

function upcomingGalas($db, $links = false) {
  $sql = "SELECT * FROM `galas` ORDER BY `galas`.`ClosingDate` ASC;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  if ($count > 0) {
    $output = "<div class=\"table-responsive\"><table class=\"table table-hover\"><thead><tr><th>Gala Name</th><th>Course</th><th>Venue</th><th>Closing Date</th><th>Last day of Gala</th><th>Gala Fee</th></tr></thead><tbody>";
    for ($i = 0; $i < $count; $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $closingDate = new DateTime($row['ClosingDate']);
      $theDate = new DateTime('now');
      $closingDate = $closingDate->format('Y-m-d');
      $theDate = $theDate->format('Y-m-d');
      if ($closingDate >= $theDate) {
        if ($links == true) {
          $output .= "<tr><td>" . $row['GalaName'] . "<br><small><a href=\"" . autoUrl("galas/competitions/" . $row['GalaID'] . "") . "\">Edit Gala</a></small></td>";
        }
        else {
          $output .= "<tr><td>" . $row['GalaName'] . "</td>";
        }
        $output .= "<td>" . $row['CourseLength'] . "</td>";
        $output .= "<td>" . $row['GalaVenue'] . "</td>";
        $output .= "<td>" . date('j F Y', strtotime($row['ClosingDate'])) . "</td>";
        $output .= "<td>" . date('j F Y', strtotime($row['GalaDate'])) . "</td>";
        if ($row['GalaFee'] > 0) {
          $output .= "<td>&pound;" . number_format($row['GalaFee'],2,'.','') . "/Swim</td></tr>";
        }
        else {
          $output .= "<td>Variable by Swim</td></tr>";
        }
      }
    }
    $output .= "</tbody></table></div>";
  }
  else {
    $output .= "<p class=\"lead\">There are no galas available to enter</p>";
  }
  return $output;
}

function upcomingGalasBySearch($db, $searchSQL = null) {
  $sql = "";
  if ($searchSQL != null) {
    $sql = "SELECT * FROM (((`galaEntries` INNER JOIN `galas` ON galaEntries.GalaID = galas.GalaID) INNER JOIN `members` ON galaEntries.MemberID = members.MemberID) INNER JOIN `squads` ON members.SquadID = squads.SquadID) WHERE " . $searchSQL . " ORDER BY `members`.`MForename`, `members`.`MSurname` ASC;";
    $result = mysqli_query($db, $sql);
    $count = mysqli_num_rows($result);
    $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
    $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
    if ($count > 0) {
      $output = "<p>" . $count . " entries for the selected competition(s)</p>";
      $output .= "<div class=\"table-responsive\"><table class=\"table table-hover\"><thead><tr><th>Gala</th><th>Swimmer</th><th>Squad</th><th>Swims</th><th>Gala Fee</th></tr></thead><tbody>";
      for ($i = 0; $i < $count; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $galaDate = new DateTime($row['GalaDate']);
        $theDate = new DateTime('now');
        $galaDate = $galaDate->format('Y-m-d');
        $theDate = $theDate->format('Y-m-d');
        $counter = 0;
        if ($galaDate >= $theDate) {
          $output .= "<tr><td>" . $row['GalaName'] . "<br><small><a href=\"" . autoUrl("galas/entries/" . $row['EntryID'] . "") . "\">View Entry</a></small></td>";
          $output .= "<td>" . $row['MForename'] . " " . $row['MSurname'] . "</td>";
          $output .= "<td>" . $row['SquadName'] . "</td>";
          $output .= "<td><ul class=\"list-unstyled\">";
          for ($j=0; $j<sizeof($swimsArray); $j++) {
            if ($row[$swimsArray[$j]] == 1) {
              $output .= "<li>" . $swimsTextArray[$j] . "</li>";
              $counter++;
            }
          }
          $output .= "</ul></td>";
          if ($row['GalaFeeConstant'] == 1) {
            $output .= "<td>&pound;" . number_format($row['GalaFee']*$counter,2,'.','') . " Total</td></tr>";
          }
          else {
            $output .= "<td>Fee Unavailable</td></tr>";
          }
        }
      }
      $output .= "</tbody></table></div>";
    }
    else {
      $output = "<p class=\"lead\">There are no entries to display</p>";
    }
  }
  else {
    $output = "<p class=\"lead\">Search for entries to display results</p>";
  }
  return $output;
}

function enteredGalas($db, $userID) {
  $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `UserID` = '$userID' ORDER BY `galas`.`GalaDate` DESC;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  if ($count > 0) {
    $output = "<div class=\"table-responsive\"><table class=\"table table-hover\"><thead><tr><th>Swimmer</th><th>Gala Name</th><th>Course</th><th>Venue</th><th><abbr title=\"Last possible date for making changes to your entry\">Closing Date</abbr></th><th>Gala Fee</th></tr></thead><tbody>";
    for ($i = 0; $i < $count; $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $endDate = new DateTime($row['GalaDate']);
      $theDate = new DateTime('now');
      $endDate = $endDate->format('Y-m-d');
      $theDate = $theDate->format('Y-m-d');
      if ($endDate >= $theDate) {
        $output .= "<tr><td><a href=\"" . autoUrl("galas/entries/" . $row['EntryID'] . "") . "\">" . $row['MForename'] . " " . $row['MSurname'] . "</a></td>";
        $output .= "<td>" . $row['GalaName'] . "</td>";
        $output .= "<td>" . $row['CourseLength'] . "</td>";
        $output .= "<td>" . $row['GalaVenue'] . "</td>";
        $output .= "<td>" . date('j F Y', strtotime($row['ClosingDate'])) . "</td>";
        if ($row['GalaFee'] > 0) {
          $output .= "<td>&pound;" . number_format($row['GalaFee'],2,'.','') . "/Swim</td></tr>";
        }
        else {
          $output .= "<td>Variable by Swim</td></tr>";
        }
      }
    }
    $output .= "</tbody></table></div>";
  }
  else {
    $output = "<p class=\"lead\">There are no upcoming galas that you have entered</p>";
  }
  return $output;
}

function enteredGalasMedia($db, $userID) {
  $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `UserID` = '$userID' ORDER BY `galas`.`GalaDate` DESC, `galas`.`ClosingDate` ASC LIMIT 4;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  if ($count > 0) {
    $output = "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
    <h2 class=\"border-bottom border-gray pb-2 mb-0\">My Entries</h2>";
    for ($i = 0; $i < 3; $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $endDate = new DateTime($row['GalaDate']);
      $theDate = new DateTime('now');
      $endDate = $endDate->format('Y-m-d');
      $theDate = $theDate->format('Y-m-d');
      if ($endDate >= $theDate) {
        $output .= "<div class=\"media text-muted pt-3\"><p class=\"media-body pb-3 mb-0 lh-125 border-bottom border-gray\"><strong class=\"d-block text-gray-dark\"><a href=\"" . autoUrl("galas/entries/" . $row['EntryID'] . "") . "\">" . $row['MForename'] . " " . $row['MSurname'] . "</a></strong>
          " . $row['GalaName'] . "
      </div>";
      }
    }
    if ($count > 3) {
      $output .= '
      <span class="d-block text-right mt-3">
            <a href="' . autoUrl('galas') . '">View all</a>
          </span></div>';
    }
    else {
      $output .= '
      </div>';
    }
  }
  else {
    $output = "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
    <h2>My Entries</h2><p class=\"mb-0\">There are no upcoming galas that you have entered.</p></div>";
  }
  return $output;
}


function closedGalas($db, $links = false) {
  $sql = "SELECT * FROM `galas` ORDER BY `galas`.`GalaDate` DESC LIMIT 0, 15;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  if ($count > 0) {
    $output = "<div class=\"table-responsive\"><table class=\"table table-hover\"><thead><tr><th>Gala Name</th><th>Course</th><th>Venue</th><th>Closing Date</th><th>Last day of Gala</th><th>Gala Fee</th></tr></thead><tbody>";
    for ($i = 0; $i < $count; $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $endDate = new DateTime($row['GalaDate']);
      $entryDate = new DateTime($row['ClosingDate']);
      $theDate = new DateTime('now');
      $endDate = $endDate->format('Y-m-d');
      $entryDate = $entryDate->format('Y-m-d');
      $theDate = $theDate->format('Y-m-d');
      if ($endDate >= $theDate && $theDate > $entryDate) {
        if ($links == true) {
          $output .= "<tr><td>" . $row['GalaName'] . "<br><small><a href=\"" . autoUrl("galas/competitions/" . $row['GalaID'] . "") . "\">Edit Gala</a></small></td>";
        }
        else {
          $output .= "<tr><td>" . $row['GalaName'] . "</td>";
        }
        $output .= "<td>" . $row['CourseLength'] . "</td>";
        $output .= "<td>" . $row['GalaVenue'] . "</td>";
        $output .= "<td>" . date('j F Y', strtotime($row['ClosingDate'])) . "</td>";
        $output .= "<td>" . date('j F Y', strtotime($row['GalaDate'])) . "</td>";
        if ($row['GalaFee'] > 0) {
          $output .= "<td>&pound;" . number_format($row['GalaFee'],2,'.','') . "/Swim</td></tr>";
        }
        else {
          $output .= "<td>Variable by Swim</td></tr>";
        }
      }
    }
    $output .= "</tbody></table></div>";
  }
  else {
    $output = "<p class=\"lead\">There are no upcoming galas with closed entries</p>";
  }
  return $output;
}

function myMonthlyFeeTable($db, $userID) {
  $sql = "SELECT squads.SquadName, squads.SquadID, squads.SquadFee FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID = '$userID' ORDER BY `squads`.`SquadFee` DESC;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  $totalsArray = [];
  $totalCost = 0;
  $reducedCost = 0;
  for ($i = 0; $i < $count; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $totalsArray[$i] = $row['SquadFee'];
    $totalCost += $totalsArray[$i];
  }
  for ($i = 0; $i < $count; $i++) {
    if ($i == 2) {
      $totalsArray[$i] = $totalsArray[$i]*0.8;
    }
    elseif ($i > 2) {
      $totalsArray[$i] = $totalsArray[$i]*0.6;
    }
    $reducedCost += $totalsArray[$i];
  }
  return "<table class=\"table table-hover\"><tr><td>The monthly subtotal is</td><td>&pound;" . number_format($totalCost,2,'.','') . "</td></tr><tr><td><strong>The monthly total payable (with any deductions) is</strong></td><td>&pound;" . number_format($reducedCost,2,'.','') . "</td></tr></table>";
}

function myMonthlyFeeMedia($db, $userID) {
  $sql = "SELECT squads.SquadName, squads.SquadID, squads.SquadFee, members.MForename, members.MSurname FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID = '$userID' ORDER BY `squads`.`SquadFee` DESC;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  $totalsArray = [];
  $squadsOutput = "";
  $totalCost = 0;
  $reducedCost = 0;
  for ($i = 0; $i < $count; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $totalsArray[$i] = $row['SquadFee'];
    $totalCost += $totalsArray[$i];
    $squadsOutput .= "<tr><td>" . $row['SquadName'] . " Squad <br>for " . $row['MForename'] . " " . $row['MSurname'] . "</td><td>&pound;" . number_format($row['SquadFee'],2,'.','') . "</td></tr>";
  }
  for ($i = 0; $i < $count; $i++) {
    if ($i == 2) {
      $totalsArray[$i] = $totalsArray[$i]*0.8;
    }
    elseif ($i > 2) {
      $totalsArray[$i] = $totalsArray[$i]*0.6;
    }
    $reducedCost += $totalsArray[$i];
  }
  $sql = "SELECT extras.ExtraName, extras.ExtraFee, members.MForename , members.MSurname FROM ((extras INNER JOIN extrasRelations ON extras.ExtraID = extrasRelations.ExtraID) INNER JOIN members ON members.MemberID = extrasRelations.MemberID) WHERE extrasRelations.UserID = '$userID' AND extras.ExtraBillPeriod = 'Month' ORDER BY `extras`.`ExtraFee` DESC;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  $monthlyExtras = "";
  $monthlyExtrasTotal = 0;
  for ($i=0; $i<$count; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $monthlyExtras .= "<tr><td>" . $row['ExtraName'] . " <br>for " . $row['MForename'] . " " . $row['MSurname'] . "</td><td>&pound;" . number_format($row['ExtraFee'],2,'.','') . "</td></tr>";
    $monthlyExtrasTotal += $row['ExtraFee'];
  }
  if ($monthlyExtrasTotal+$reducedCost > 0) {
    $output = "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
    <h2>My Fees</h2><p class=\"lead border-bottom border-gray pb-2 mb-0\">Showing monthly fees</p>
    <div class=\"table-responsive\"><table class=\"table mb-0\">
    <tbody>
    <tr><td>The monthly subtotal for Squad Fees is</td><td>&pound;" . number_format($totalCost,2,'.','') . "</td></tr>";
    if (($totalCost - $reducedCost) > 0) {
      $output .= "<tr><td>The monthly total payable for squads (with any deductions) is</td><td>&pound;" . number_format($reducedCost,2,'.','') . "</td></tr>";
    }
    $output .= "<tr><td>The monthly total for extras is</td><td>&pound;" . number_format($monthlyExtrasTotal,2,'.','') . "</td></tr>
    <tr class=\"bg-light\"><td><strong>The monthly total is</strong></td><td>&pound;" . number_format(($reducedCost + $monthlyExtrasTotal),2,'.','') . "</td></tr>
    </tbody></table></div>
    </div>";
    return $output;
  }
  else {
    return "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
    <h2>My Fees</h2><p class=\"mb-0\">You have no monthly fees to pay. You may need to add a swimmer to see any fees.</p>
    </div>";
  }
}

function adminSwimmersTable($db, $squadID = null) {
  if ($squadID != null) {
    $sqlSwim = "SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, squads.SquadName, members.DateOfBirth FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.SquadID = '$squadID' ORDER BY `members`.`MForename` , `members`.`MSurname` ASC ;";
  }
  else {
    $sqlSwim = "SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, squads.SquadName, members.DateOfBirth FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) ORDER BY `members`.`MForename` , `members`.`MSurname` ASC;";
  }
  $result = mysqli_query($db, $sqlSwim);
  $swimmerCount = mysqli_num_rows($result);
  if ($swimmerCount > 0) {
    $output = '
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Squad</th>
            <th>Date of Birth</th>
            <th>Age</th>
            <th><abbr title="Age at end of year">AEoY</abbr></th>
            <th>ASA Number</th>
          </tr>
        </thead>
        <tbody>';
    $resultX = mysqli_query($db, $sqlSwim);
    for ($i = 0; $i < $swimmerCount; $i++) {
      $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
      $swimmerLink = autoUrl("swimmers/" . $swimmersRowX['MemberID'] . "");
      $DOB = date('j F Y', strtotime($swimmersRowX['DateOfBirth']));
      $age = date_diff(date_create($swimmersRowX['DateOfBirth']), date_create('today'))->y;
      $ageEoY = date('Y') - date('Y', strtotime($swimmersRowX['DateOfBirth']));
      $output .= "<tr>
        <td><a href=\"" . $swimmerLink . "\">" . $swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname'] . "</a></td>
        <td>" . $swimmersRowX['SquadName'] . "</td>
        <td>" . $DOB . "</td>
        <td>" . $age . "</td>
        <td>" . $ageEoY . "</td>
        <td><a href=\"https://www.swimmingresults.org/biogs/biogs_details.php?tiref=" . $swimmersRowX['ASANumber'] . "\" target=\"_blank\" title=\"ASA Biographical Data\">" . $swimmersRowX['ASANumber'] . " <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a></td>
      </tr>";
    }
    $output .= '
        </tbody>
      </table>
    </div>';
  }
  else {
    $output = "<div class=\"alert alert-warning\"><strong>No members found for that squad</strong> <br>Please try another search</div>";
  }
  return $output;
}

function squadInfoTable($db, $enableLinks = false) {
  $sql = "SELECT squads.SquadID, squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC FROM squads ORDER BY `squads`.`SquadFee` DESC;";
  $result = mysqli_query($db, $sql);
  $count = mysqli_num_rows($result);
  $output = "";
  if ($count > 0) {
    $output = '
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Fee</th>
            <th>Coach</th>
            <th>Timetable</th>
            <th>Code of Conduct</th>
          </tr>
        </thead>
        <tbody>';
    $result = mysqli_query($db, $sql);
    for ($i = 0; $i < $count; $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $output .= "<tr>";
      if ($enableLinks) {
        $output .= "<td><a href=\"" . autoUrl("squads/" . $row['SquadID']) . "\">" . $row['SquadName'] . "</a></td>";
      }
      else {
        $output .= "<td>" . $row['SquadName'] . "</td>";
      }
        $output .= "<td>&pound;" . $row['SquadFee'] . "</td>
        <td>" . $row['SquadCoach'] . "</td>
        <td>";
        if ($row['SquadTimetable'] != "") {
          $output .= "<a href=\"" . $row['SquadTimetable'] . "\" target=\"_blank\">Timetable</a>";
        }
          $output .= "</td>
        <td>";
        if ($row['SquadCoC'] != "") {
          $output .= "<a href=\"" . $row['SquadCoC'] . "\" target=\"_blank\">Code of Conduct</a>";
        }
        $output .= "</td>
      </tr>";
    }
    $output .= '
        </tbody>
      </table>
    </div>';
  }
  return $output;
}


function autoUrl($relative) {
  // Returns an absolute URL
  global $rootURL;
  return $rootURL . $relative;
}



$count = 0;

if ( (empty($_SESSION['LoggedIn']) || empty($_SESSION['Username'])) && ($preventLoginRedirect != true)) {
  // Allow access to main page
  header("Location: " . autoUrl("login.php"));
}
elseif (((!empty($_SESSION['LoggedIn'])) || (!empty($_SESSION['Username']))) && ($preventLoginRedirect == true)) {
  // Don't show login etc if logged in
  header("Location: " . autoUrl("index.php"));
}

?>
