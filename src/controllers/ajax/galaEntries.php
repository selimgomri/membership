<?php
include_once "../database.php";
$access = $_SESSION['AccessLevel'];
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
      $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) AND members.MSurname LIKE '%$search%' ORDER BY galas.ClosingDate ASC, galas.GalaDate DESC;";
    }
    else {
      $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) AND galas.GalaID = '$galaID' AND members.MSurname LIKE '%$search%';";
    }
  }
  elseif ((!isset($_REQUEST["galaID"])) && (isset($_REQUEST["search"]))) {
    // get the search term parameter from request
    $search = mysqli_real_escape_string($link, $_REQUEST["search"]);

    // Search the database for the results
    $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) AND members.MSurname LIKE '%$search%';";
  }
  elseif ((isset($_REQUEST["galaID"])) && (!isset($_REQUEST["search"]))) {
    // get the search term parameter from request
    $galaID = mysqli_real_escape_string($link, $_REQUEST["galaID"]);

    // Search the database for the results
    $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galas.GalaDate >= CURDATE( ) AND galas.GalaID = '$galaID';";
  }
  else {
    // Error
    echo "<p>Disaster. The GET request was funny, so try again.</p>";
  }

  if ($galaID == "Select a gala") {
    echo '<div class="ajaxPlaceholder"><strong>Select a Gala</strong> <br>We\'ll be able to load entries if you select a gala</div>';
  }
  else {

    $result = mysqli_query($link, $sql);
    $count = mysqli_num_rows($result);


    $content = '<table class="table table-hover"><thead><tr><th>Swimmer</th><th>Swims</th><th><abbr title="Tick to prevent editing this entry">Processed?</abbr></th></tr></thead><tbody>';

    // For loop iterates through the rows of the database result, producing rows for the table
    for ($i=0; $i<$count; $i++) {

      // Fetches the row as an array
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

      $hyTekPrintDate = "";
      if ($row['HyTek'] == 1) {
        $hyTekPrintDate = " <br>DoB: " . date('j F Y', strtotime($row['DateOfBirth'])) . "";
      }

      // First part of the row content
      $content .= "<tr><td><strong>" . $row['MForename'] . " " . $row['MSurname']  . "</strong>" . $hyTekPrintDate . "<br><a href=\"https://www.swimmingresults.org/biogs/biogs_details.php?tiref=" . $row['ASANumber'] . "\" target=\"_blank\" title=\"Click to see times\">" . $row['ASANumber'] . " <i class=\"fa fa-external-$link\" aria-hidden=\"true\"></i></a><br>
      <span class=\"small\">" . $row['GalaName'] . "<br><a href=\"" . autoUrl('galas/entries/' . $row['EntryID']) . "\">Edit Entry</a></span></td>";

      // Arrays of swims used to check whever to print the name of the swim entered
      // BEWARE This is in an order to ease inputting data into SportSystems, contrary to these arrays in other files
      $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','150IM','200IM','400IM',];
      $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Back','100 Back','200 Back','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','100 IM','150 IM','200 IM','400 IM',];
      $swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BackTime','100BackTime','200BackTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','100IMTime','150IMTime','200IMTime','400IMTime',];


      // Create the cell and unordered list
      $content .= "<td><ul class=\"mb-0 list-unstyled\">";

      // Print <li>Swim Name</li> for each entry
      if ($row['TimesRequired']!=1) {
        for ($y=0; $y<sizeof($swimsArray); $y++) {
          if ($row[$swimsArray[$y]] == 1) {
            $content .= "<li>" . $swimsTextArray[$y] . "</li>";
          }
        }
      }
      elseif ($row['TimesRequired']==1) {
        for ($y=0; $y<sizeof($swimsArray); $y++) {
          if ($row[$swimsArray[$y]] == 1) {
            $content .= "<li><strong>" . $swimsTextArray[$y] . "</strong> <br>" . $row[$swimsTimeArray[$y]] . "</li>";
          }
        }
      }
      // End ul and cell
      $content .= "</ul></td>";

      // If the entry has been processes, show a ticked checkbox
      if ($row['EntryProcessed'] == 1) {
        $content .= "<td>
        <div class=\"custom-control custom-checkbox\">
          <input type=\"checkbox\" value=\"1\" checked class=\"custom-control-input\" id=\"processedEntry-" . $row['EntryID'] . "\">
          <label class=\"custom-control-label\" for=\"processedEntry-" . $row['EntryID'] . "\">Processed?</label>
        </div></td>";
      }

      // Else output an empty cell
      else {
        $content .= "<td>
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
  echo "Access not allowed";
}
?>
