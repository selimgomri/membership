<?php
include_once "../database.php";
$access = $_SESSION['AccessLevel'];
$count = 0;
if ($access == "Committee" || $access == "Admin" || $access == "Coach" || $access == "Galas") {
  $sql = "";
  if ((isset($_POST["squadID"])) && (isset($_POST["search"]))) {
    // get the squadID parameter from post
    $squadID = mysqli_real_escape_string($link, htmlentities($_POST["squadID"]));
    // get the search term parameter from post
    $search = mysqli_real_escape_string($link, htmlentities($_POST["search"]));

    // Search the database for the results
		if ($squadID == "allSquads") {
	    $sql = "SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, squads.SquadName, members.DateOfBirth FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MSurname LIKE '%$search%' ORDER BY `members`.`MForename` , `members`.`MSurname` ASC ;";
	  }
	  else {
	    $sql = "SELECT members.MemberID, members.MForename, members.MSurname, members.ASANumber, squads.SquadName, members.DateOfBirth FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE squads.SquadID = '$squadID' AND members.MSurname LIKE '%$search%' ORDER BY `members`.`MForename` , `members`.`MSurname` ASC;";
	  }
  }

  $result = mysqli_query($link, $sql);
  $swimmerCount = mysqli_num_rows($result);
  if ($swimmerCount > 0) {
    $output = '
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Squad</th>
            <th><abbr title="4 Week Rolling Attendance">Attendance</abbr></th>
          </tr>
        </thead>
        <tbody>';
    $resultX = mysqli_query($link, $sql);
    for ($i = 0; $i < $swimmerCount; $i++) {
      $swimmersRowX = mysqli_fetch_array($resultX, MYSQLI_ASSOC);
      $swimmer$link = autoUrl("attendance/history/swimmers/" . $swimmersRowX['MemberID'] . "");
      $DOB = date('j F Y', strtotime($swimmersRowX['DateOfBirth']));
      $age = date_diff(date_create($swimmersRowX['DateOfBirth']), date_create('today'))->y;
      $ageEoY = date('Y') - date('Y', strtotime($swimmersRowX['DateOfBirth']));
      $output .= "<tr>
        <td><a href=\"" . $swimmer$link . "\">" . $swimmersRowX['MForename'] . " " . $swimmersRowX['MSurname'] . "</a></td>
        <td>" . $swimmersRowX['SquadName'] . "</td>
        <td>" . getAttendanceByID($link, $swimmersRowX['MemberID'], 4) . "%</td>
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
  echo "Access not allowed";
}
?>
