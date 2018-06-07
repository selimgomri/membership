<?php
$access = $_SESSION['AccessLevel'];
$swimmerCount = 0;

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
  if (isset($_REQUEST["squadID"])) {
    // get the squadID parameter from URL
    $squadID = mysqli_real_escape_string($link, $_REQUEST["squadID"]);

    $response = "";

    if ($squadID != null) {
      $sql = "SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE squads.SquadID = '$squadID' AND ((sessions.DisplayFrom <= CURDATE( )) AND (sessions.DisplayUntil >= CURDATE( ))) ORDER BY sessions.SessionDay ASC, sessions.StartTime ASC;";
      $result = mysqli_query($link, $sql);
      $swimmerCount = mysqli_num_rows($result);
      $content = '<option>Choose the session from the menu</option>';
      for ($i=0; $i<$swimmerCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $dayText = "";
        switch ($row['SessionDay']) {
            case 0:
                $dayText = "Sunday";
                break;
            case 1:
                $dayText = "Monday";
                break;
            case 2:
                $dayText = "Tuesday";
                break;
            case 3:
                $dayText = "Wednesday";
                break;
            case 4:
                $dayText = "Thursday";
                break;
            case 5:
                $dayText = "Friday";
                break;
            case 6:
                $dayText = "Saturday";
                break;
        }

        $content .= "<option value=\"" . $row['SessionID'] . "\">" . $row['SessionName'] . ", " . $dayText . " at " . $row['StartTime'] . "</option>";
      }
    }

    if ($swimmerCount > 0) {
      echo $content;
    }
    else {
      echo "<option selected value=\"0\">Couldn't find anything</option>";
    }
  }

  if (isset($_REQUEST["sessionID"])) {
      $date = "4";
    if (isset($_REQUEST["date"])) {
      $dateO = $date = mysqli_real_escape_string($_REQUEST["date"]);
    }

    $sessionID = mysqli_real_escape_string($link, $_REQUEST["sessionID"]);
    $response = $content = $modalOutput = "";

    if ($sessionID != null) {
      // Check if the register has been done before
      $sql = "SELECT `SessionID` FROM `sessionsAttendance` WHERE `WeekID` = '$date' AND `SessionID` = '$sessionID';";
      $result = mysqli_query($link, $sql);
      $sessionRecordExists = mysqli_num_rows($result);
      $sql = "SELECT `WeekDateBeginning` FROM `sessionsWeek` WHERE `WeekID` = '$date';";
      $result = mysqli_query($link, $sql);
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $weekBeginning = $row['WeekDateBeginning'];
      $sql = "SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE sessions.SessionID = '$sessionID';";
      $result = mysqli_query($link, $sql);
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $dayAdd = $row['SessionDay'];
      $date = date ('j F Y', strtotime($weekBeginning. ' + ' . $dayAdd . ' days'));

      $dayText = "";
      switch ($row['SessionDay']) {
          case 0:
              $dayText = "Sunday";
              break;
          case 1:
              $dayText = "Monday";
              break;
          case 2:
              $dayText = "Tuesday";
              break;
          case 3:
              $dayText = "Wednesday";
              break;
          case 4:
              $dayText = "Thursday";
              break;
          case 5:
              $dayText = "Friday";
              break;
          case 6:
              $dayText = "Saturday";
              break;
      }

      $content .= "<h2>Take register</h2><p>for " . $row['SquadName'] . " Squad, " . $row['SessionName'] . " on " . $dayText . " " . $date . " at " . $row['StartTime'] . "</p>";
      $datetime1 = new DateTime($row['StartTime']);
      $datetime2 = new DateTime($row['EndTime']);
      $interval = $datetime1->diff($datetime2);
      $content .= "<p>This session is " . $interval->format('%h hours %I minutes') . " long, finishing at " . $row['EndTime'] . "</p>";
      $sql = "SELECT * FROM ((sessions INNER JOIN members ON sessions.SquadID = members.SquadID) INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE sessions.SessionID = '$sessionID' ORDER BY members.MForename, members.MSurname ASC";
      $result = mysqli_query($link, $sql);
      $swimmerCount = mysqli_num_rows($result);
      $content .= "<div class=\"table-responsive\"><table class=\"table table-striped\"><thead><tr><th>Name</th><th>Notes</th></tr></thead><tbody>";
      for ($i=0; $i<$swimmerCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $checked = "";
        if ($sessionRecordExists > 0) {
          $member = $row['MemberID'];
          $sqlHistory = "SELECT * FROM `sessionsAttendance` WHERE `WeekID` = '$dateO' AND `SessionID` = '$sessionID' AND `MemberID` = '$member';";
          $resultHistory = mysqli_query($link, $sqlHistory);
          $existsCount = mysqli_num_rows($resultHistory);
          $rowHistory = mysqli_fetch_array($resultHistory, MYSQLI_ASSOC);
          if ($rowHistory['AttendanceBoolean'] == 1) {
            $checked = "checked";
          }
        }
        $content .= "
        <tr>
          <td>
            <div class=\"custom-control custom-checkbox\">
            <input type=\"checkbox\" class=\"custom-control-input\" " . $checked . " name=\"Member-" . $row['MemberID'] . "\" value=\"1\" id=\"Member-" . $row['MemberID'] . "\">
            <label class=\"custom-control-label\" for=\"Member-" . $row['MemberID'] . "\">" . $row['MForename'] . " " . $row['MSurname'] . "</label>
            </div>
          </td>
          <td>";
          if ($row['MedicalNotes'] != "") {
            //ref=\"" . autoUrl("swimmers/" . $row['MemberID']) . "\" target=\"_blank\">
            $content .= "<a data-toggle=\"modal\" href=\"#medicalModal" . $row['MemberID'] . "\"><span class=\"badge badge-danger\">MEDICAL</span></a>";
            $modalOutput .= '
            <!-- Modal -->
            <div class="modal fade" id="medicalModal' . $row['MemberID'] . '" tabindex="-1" role="dialog" aria-labelledby="medicalModalTitle' . $row['MemberID'] . '" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="medicalModalTitle' . $row['MemberID'] . '">Medical Information for ' . $row['MForename'] . ' ' . $row['MSurname'] . '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  ' . $row['MedicalNotes'] . '
                  </div>
                </div>
              </div>
            </div>
            ';
          }
          if ($row['OtherNotes'] != "") {
            $content .= " <a data-toggle=\"modal\" href=\"#notesModal" . $row['MemberID'] . "\"><span class=\"badge badge-info\">OTHER</span></a>";
            $modalOutput .= '
            <!-- Modal -->
            <div class="modal fade" id="notesModal' . $row['MemberID'] . '" tabindex="-1" role="dialog" aria-labelledby="notesModalTitle' . $row['MemberID'] . '" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="notesModalTitle' . $row['MemberID'] . '">Other Notes for ' . $row['MForename'] . ' ' . $row['MSurname'] . '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  ' . $row['OtherNotes'] . '
                  </div>
                </div>
              </div>
            </div>
            ';
          }
          $content .= "
          </td>
        </tr>";
      }
      $content .= '</tbody></table></div><p class="mb-0"><button type="submit" class="btn btn-outline-dark">Save Register</button></p>';
    }

    if ($swimmerCount > 0) {
      echo $content;
      echo $modalOutput;
    }
    else {
      echo "<p class=\"lead\">No swimmers were found for this squad and session</p>";
    }
  }

}
else {
  halt(404);
}
?>
