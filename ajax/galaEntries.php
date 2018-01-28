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
    $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galas.GalaID = '$galaID' AND members.MSurname LIKE '%$search%';";
  }
  elseif ((!isset($_REQUEST["galaID"])) && (isset($_REQUEST["search"]))) {
    // get the search term parameter from request
    $search = mysqli_real_escape_string($link, $_REQUEST["search"]);

    // Search the database for the results
    $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE members.MSurname LIKE '%$search%';";
  }
  elseif ((isset($_REQUEST["galaID"])) && (!isset($_REQUEST["search"]))) {
    // get the search term parameter from request
    $galaID = mysqli_real_escape_string($link, $_REQUEST["galaID"]);

    // Search the database for the results
    $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galas.GalaID = '$galaID';";
  }
  else {
    // Error
    echo "<p>Disaster. The GET was funny, so try again.</p>";
  }

  $result = mysqli_query($link, $sql);
  $count = mysqli_num_rows($result);


  $content = '<table class="table table-hover"><thead><tr><th>Name</th><th>ASA Number</th><th>Gala</th><th>Swims</th><th>Processed?</th></tr></thead><tbody>';

  // For loop iterates through the rows of the database result, producing rows for the table
  for ($i=0; $i<$count; $i++) {

    // Fetches the row as an array
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    // First part of the row content
    $content .= "<tr><td>" . $row['MForename'] . " " . $row['MSurname'] . "</td><td>" . $row['ASANumber'] . "</td><td>" . $row['GalaName'] . "</td>";

    // Arrays of swims used to check whever to print the name of the swim entered
    $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
    $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];

    // Create the cell and unordered list
    $content .= "<td><ul>";

    // Print <li>Swim Name</li> for each entry
    for ($y=0; $y<sizeof($swimsArray); $y++) {
      if ($row[$swimsArray[$y]] == 1) {
        $content .= "<li>" . $swimsTextArray[$y] . "</li>";
      }
    }

    // End ul and cell
    $content .= "</ul></td>";

    // If the entry has been processes, show a ticked checkbox
    if ($row['EntryProcessed'] == 1) {
      $content .= "<td><i class=\"fa fa-check\" aria-hidden=\"true\"></i></td>";
    }

    // Else output an empty cell
    else {
      $content .= "<td></td>";
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
else {
  echo "Access not allowed";
}
?>
