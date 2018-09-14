<?php

use CLSASC\EquivalentTime\EquivalentTime;

$access = $_SESSION['AccessLevel'];
$sex = mysqli_real_escape_string($link, $_REQUEST["sex"]);
if ($sex == "all") {
  $sex = "";
} else if ($sex == "m") {
  $sex = " AND `Gender` = 'Male' ";
} else if ($sex == "f") {
  $sex = " AND `Gender` = 'Female' ";
} else {
  halt(500);
}

$count = 0;
if ($access == "Committee" || $access == "Admin" || $access == "Coach" || $access == "Galas") {
  $sql = "";
  if ((isset($_REQUEST["galaID"])) && (isset($_REQUEST["search"]))) {
    // get the galaID parameter from request
    $galaID = mysqli_real_escape_string($link, $_REQUEST["galaID"]);
    // get the search term parameter from request
    $search = mysqli_real_escape_string($link, $_REQUEST["search"]);

    // Search the database for the results
    if ($galaID == "allGalas") {
      $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON
      galaEntries.MemberID = members.MemberID) INNER JOIN galas ON
      galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) " .
      $sex . " AND members.MSurname LIKE '%$search%' ORDER BY galas.ClosingDate
      ASC, galas.GalaDate DESC;";
    }
    else {
      $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON
      galaEntries.MemberID = members.MemberID) INNER JOIN galas ON
      galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) " .
      $sex . " AND galas.GalaID = '$galaID' AND members.MSurname LIKE
      '%$search%';";
    }
  }
  elseif ((!isset($_REQUEST["galaID"])) && (isset($_REQUEST["search"]))) {
    // get the search term parameter from request
    $search = mysqli_real_escape_string($link, $_REQUEST["search"]);

    // Search the database for the results
    $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON
    galaEntries.MemberID = members.MemberID) INNER JOIN galas ON
    galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) " .
    $sex . " AND members.MSurname LIKE '%$search%';";
  }
  elseif ((isset($_REQUEST["galaID"])) && (!isset($_REQUEST["search"]))) {
    // get the search term parameter from request
    $galaID = mysqli_real_escape_string($link, $_REQUEST["galaID"]);

    // Search the database for the results
    $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON
    galaEntries.MemberID = members.MemberID) INNER JOIN galas ON
    galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) " .
    $sex . " AND galas.GalaID = '$galaID';";
  }
  else {
    // Error
    halt(404);
  }

  if ($galaID == "Select a gala") {
    echo '<div class="ajaxPlaceholder"><strong>Select a Gala</strong> <br>We\'ll be able to load entries if you select a gala</div>';
  }
  else {

    $result = mysqli_query($link, $sql);
    $count = mysqli_num_rows($result);


    $content = "";
    if (app('request')->isMobile()) {
      $content .= '<table class="table table-sm">';
    } else {
      $content .= '<table class="table table-hover">';
    }
    $content .= '<thead class="thead-light"><tr><th>Swimmer</th><th>Swims</th><th class="d-print-none"><abbr title="Tick to prevent editing this entry">Processed?</abbr></th></tr></thead><tbody>';

    // For loop iterates through the rows of the database result, producing rows for the table
    for ($i=0; $i<$count; $i++) {

      // Fetches the row as an array
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

      $member = mysqli_real_escape_string($link, $row['MemberID']);
      $times = null;

      $hyTekPrintDate = "";
      if ($row['HyTek'] == 1) {
        $hyTekPrintDate = " <br>DoB: " . date('j F Y', strtotime($row['DateOfBirth'])) . "";
        $type; $typeB;
        if ($row['CourseLength'] == "SHORT") {
          $type = "SCPB";
          $typeB = "LCPB";
        } else {
          $type = "LCPB";
          $typeB = "SCPB";
        }
        $times = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `times`
        WHERE `MemberID` = '$member' AND `Type` = '$type';"), MYSQLI_ASSOC);
        $timesB = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `times`
        WHERE `MemberID` = '$member' AND `Type` = '$typeB';"), MYSQLI_ASSOC);
      }

      // First part of the row content
      $content .= "<tr><td><strong>" . $row['MForename'] . " " .
      $row['MSurname']  . "</strong>" . $hyTekPrintDate . "<br><a
      class=\"d-print-none\"
      href=\"https://www.swimmingresults.org/biogs/biogs_details.php?tiref=" .
      $row['ASANumber'] . "\" target=\"_blank\" title=\"Click to see times\">" .
      $row['ASANumber'] . " <i class=\"fa fa-external-link\"
      aria-hidden=\"true\"></i></a><span class=\"d-none d-print-inline\">ASA: " .
      $row['ASANumber'] . "</span><br>
      <span class=\"small\">" . $row['GalaName'] . "<br><a class=\"d-print-none\" href=\"" . autoUrl('galas/entries/' . $row['EntryID']) . "\">Edit Entry</a><br><a class=\"d-print-none\" href=\"" . autoUrl('galas/entries/' . $row['EntryID']) . "/manualtime\">Set Manual Times</a></span></td>";

      // Arrays of swims used to check whever to print the name of the swim entered
      // BEWARE This is in an order to ease inputting data into SportSystems, contrary to these arrays in other files
      $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','150IM','200IM','400IM',];
      $swimsTextArray = ['50&nbsp;Free','100&nbsp;Free','200&nbsp;Free','400&nbsp;Free','800&nbsp;Free','1500&nbsp;Free','50&nbsp;Back','100&nbsp;Back','200&nbsp;Back','50&nbsp;Breast','100&nbsp;Breast','200&nbsp;Breast','50&nbsp;Fly','100&nbsp;Fly','200&nbsp;Fly','100&nbsp;IM','150&nbsp;IM','200&nbsp;IM','400&nbsp;IM',];
      $swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','150IMTime','200IMTime','400IMTime',];


      // Create the cell and unordered list
      $content .= "<td><ul class=\"mb-0 list-unstyled\">";

      // Print <li>Swim Name</li> for each entry
      if ($row['HyTek'] != 1) {
        for ($y=0; $y<sizeof($swimsArray); $y++) {
          if ($row[$swimsArray[$y]] == 1) {
            $content .= "<li>" . $swimsTextArray[$y] . "</li>";
          }
        }
      }
      else {
        for ($y=0; $y<sizeof($swimsArray); $y++) {
          if ($row[$swimsArray[$y]] == 1) {
            $course; $to; $time;
            if ($row['CourseLength'] == "SHORT") {
              $course = "50m";
              $to = "25m";
            } else {
              $course = "25m";
              $to = "50m";
            }
            $output;
            if ($timesB[$swimsArray[$y]] != "") {
              $time = explode(".", $timesB[$swimsArray[$y]]);
              $ms = explode(":", $time[0]);
              $mins = $secs = 0;
              if (sizeof($ms) == 1) {
                $secs = $ms[0];
              } else {
                $mins = $ms[0];
                $secs = $ms[1];
              }
              $hunds = $time[1];
              $time_in = (double) 60*$mins + $secs + ($hunds/100);
              try {
              	$time = new EquivalentTime($course, str_replace('&nbsp;', ' ', $swimsTextArray[$y]), $time_in);
                $time_double = $time->getConversion($to);
              	$time->setOutputAsString(true);
                if ($time_double < $time_in) {
                  // echo $time_double . " " . $time_in;
                  $output = ', <abbr title="Faster converted time available">FC:</abbr> ' . $time->getConversion($to);
                }
              } catch (Exception $e) {
              	// Do nothing
              }
            }
            $content .= "<li><strong>" . $swimsTextArray[$y] . "</strong> <br>";
            if ($times[$swimsArray[$y]] != "") {
              $content .= $times[$swimsArray[$y]] . $output;;
            } else if ($row[$swimsTimeArray[$y]]) {
              $content .= $row[$swimsTimeArray[$y]] . $output;
            } else {
              $content .= "No Time Available";
            }
            $content .= "</li>";
          }
        }
      }
      // End ul and cell
      $content .= "</ul></td>";

      // If the entry has been processes, show a ticked checkbox
      if ($row['EntryProcessed'] == 1) {
        $content .= "<td class=\"d-print-none\">
        <div class=\"custom-control custom-checkbox\">
          <input type=\"checkbox\" value=\"1\" checked class=\"custom-control-input\" id=\"processedEntry-" . $row['EntryID'] . "\">
          <label class=\"custom-control-label\" for=\"processedEntry-" . $row['EntryID'] . "\">Processed?</label>
        </div></td>";
      }

      // Else output an empty cell
      else {
        $content .= "<td class=\"d-print-none\">
        <div class=\"custom-control custom-checkbox\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"processedEntry-" . $row['EntryID'] . "\">
          <label class=\"custom-control-label\" for=\"processedEntry-" . $row['EntryID'] . "\">Processed?</label>
        </div></td>";
      }

      // End the row
      $content .= "</tr>";
    }
    $content .= '</tbody></table>';

    // Output to browser for AJAX
    if ($count > 0) {
      echo $content;
    }
    else {
      echo '<div class="alert alert-warning"><strong>We could not find any entries matching that search</strong> <br>Try another search by selecting a new gala or changing the surname you searched for</div>';
    }
  }
}
else {
  halt(404);
}
?>
