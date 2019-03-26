<?php
$access = $_SESSION['AccessLevel'];
$count = 0;
if ($access == "Committee" || $access == "Admin" || $access == "Coach" || $access == "Galas") {
  $sql = "";
  if ((isset($_POST["squadID"])) && (isset($_POST["search"]))) {
    // get the squadID parameter from post
    $squadID = mysqli_real_escape_string($link, htmlentities($_POST["squadID"]));
    // get the search term parameter from post
    $search = mysqli_real_escape_string($link, htmlentities($_POST["search"]));

    if ($_POST['type'] == "orphan") {
      // Search the database for the results
  		if ($squadID == "allSquads") {
  	    $sql = "SELECT members.MemberID, members.MForename, members.MSurname,
  	    members.ASANumber, squads.SquadName, members.DateOfBirth, squads.SquadID
  	    FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID)
  	    WHERE members.UserID IS NULL AND members.MSurname LIKE '%$search%' ORDER
  	    BY `members`.`MForename` , `members`.`MSurname` ASC ;";
  	  }
  	  else {
  	    $sql = "SELECT members.MemberID, members.MForename, members.MSurname,
  	    members.ASANumber, squads.SquadName, members.DateOfBirth FROM (members
  	    INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE
  	    members.UserID IS NULL AND squads.SquadID = '$squadID' AND
  	    members.MSurname LIKE '%$search%' ORDER BY `members`.`MForename` ,
  	    `members`.`MSurname` ASC;";
  	  }
    } else {
      if ($squadID == "allSquads") {
  	    $sql = "SELECT members.MemberID, members.MForename, members.MSurname,
  	    members.ASANumber, squads.SquadName, members.DateOfBirth, squads.SquadID
  	    FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID)
  	    WHERE members.MSurname LIKE '%$search%' ORDER BY `members`.`MForename` ,
  	    `members`.`MSurname` ASC ;";
  	  }
  	  else {
  	    $sql = "SELECT members.MemberID, members.MForename, members.MSurname,
  	    members.ASANumber, squads.SquadName, members.DateOfBirth FROM (members
  	    INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE
  	    squads.SquadID = '$squadID' AND members.MSurname LIKE '%$search%' ORDER
  	    BY `members`.`MForename` , `members`.`MSurname` ASC;";
  	  }
    }
  }

  $result = mysqli_query($link, $sql);
  $swimmerCount = mysqli_num_rows($result);
  if ($swimmerCount > 0) {
    $output = '
    <div class="table-responsive-md">';
    if (app('request')->isMobile()) {
      $output.= '<table class="table table-sm">';
    } else {
      $output .= '<table class="table">';
    }
    $output .= '
        <thead class="thead-light table-sticky">
          <tr>
            <th>Name</th>
            <th>Squad</th>
            <th>Date of Birth</th>
            <th>Age</th>
            <th><abbr title="Age at end of year">AEoY</abbr></th>
            <th><abbr title="4 Week Rolling Attendance">Attendance</abbr></th>
            <th>ASA Number</th>
          </tr>
        </thead>
        <tbody>';
    $resultX = mysqli_query($link, $sql);
    for ($i = 0; $i < $swimmerCount; $i++) {
      $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
      $swimmerLink = autoUrl("swimmers/" . $swimmersRowX['MemberID'] . "");
      $DOB = date('j F Y', strtotime($swimmersRowX['DateOfBirth']));
      $age = date_diff(date_create($swimmersRowX['DateOfBirth']), date_create('today'))->y;
      $ageEoY = date('Y') - date('Y', strtotime($swimmersRowX['DateOfBirth']));
      $output .= "<tr>
        <td><a href=\"" . $swimmerLink . "\">" . htmlspecialchars($swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname']) . "</a></td>
        <td><a href=\"" . autoUrl('squads/' . $swimmersRowX['SquadID']) . "\">" . htmlspecialchars($swimmersRowX['SquadName']) . "</a></td>
        <td>" . $DOB . "</td>
        <td>" . $age . "</td>
        <td>" . $ageEoY . "</td>
        <td><a href=\"" . autoUrl('attendance/history/swimmers/' . $swimmersRowX['MemberID']) . "\">" . getAttendanceByID($link, $swimmersRowX['MemberID'], 4) . "%</a></td>
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
	echo $output;
}
else {
  halt(404);
}
?>
