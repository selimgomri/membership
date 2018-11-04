<?php

use Respect\Validation\Validator as v;

$access = $_SESSION['AccessLevel'];
$swimmerCount = 0;

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
  if (isset($_REQUEST["squadID"]) && v::intVal()->validate($_REQUEST["squadID"])) {
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

        $content .= "<option value=\"" . $row['SessionID'] . "\">" . $row['SessionName'] . ", " . $dayText . " at " . (new DateTime($row['StartTime']))->format("H:i") . "</option>";
      }
    }

    if ($swimmerCount > 0) {
      echo $content;
    }
    else {
      echo "<option selected value=\"0\">Couldn't find anything</option>";
    }
  }

  if (isset($_REQUEST["sessionID"]) && v::intVal()->validate($_REQUEST["sessionID"])) {
    if (isset($_REQUEST["date"]) && v::intVal()->validate($_REQUEST["date"])) {
      $dateO = $date = mysqli_real_escape_string($link, $_REQUEST["date"]);
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

      $datetime1 = new DateTime($row['StartTime']);
      $datetime2 = new DateTime($row['EndTime']);
      $content .= "<h2>Take register</h2><p>for " . $row['SquadName'] . " Squad, " . $row['SessionName'] . " on " . $dayText . " " . $date . " at " . $datetime1->format("H:i") . "</p>";
      $interval = $datetime1->diff($datetime2);
      $content .= "<p>This session is ";

      if ((int) $interval->format('%h') == 1) {
        $content .= "1 hour ";
      } else if ((int) $interval->format('%h') > 1) {
        $content .= $interval->format('%h') . " hours ";
      }

      if ((int) $interval->format('%I') == 1) {
        $content .= "1 minute ";
      } else if ((int) $interval->format('%I') > 1) {
        $content .= $interval->format('%I') . " minutes ";
      }

      $content .= "long, finishing at " . $datetime2->format("H:i") . "</p>";
      $sql = "SELECT members.UserID, members.MemberID, members.MForename, members.MSurname,
      members.DateOfBirth, members.OtherNotes,
      memberPhotography.Website, memberPhotography.Social,
      memberPhotography.Noticeboard, memberPhotography.FilmTraining,
      memberPhotography.ProPhoto, memberMedical.Conditions, memberMedical.Allergies,
      memberMedical.Medication FROM ((((sessions INNER JOIN members ON
      sessions.SquadID = members.SquadID) INNER JOIN squads ON sessions.SquadID =
      squads.SquadID) LEFT JOIN `memberPhotography` ON members.MemberID =
      memberPhotography.MemberID) LEFT JOIN `memberMedical` ON members.MemberID =
      memberMedical.MemberID) WHERE sessions.SessionID = '$sessionID' AND members.Status = '1' ORDER BY
      members.MForename, members.MSurname ASC";
      $result = mysqli_query($link, $sql);
      $swimmerCount = mysqli_num_rows($result);
      $content .= "<div class=\"table-responsive-md table-nomargin mb-1\">";
      if (app('request')->isMobile()) {
        $content .= '<table class="table">';
      } else {
        $content .= '<table class="table">';
      }
      $content .= "<thead class=\"thead-light\"><tr><th>Swimmer</th><th>Notes</th></tr></thead><tbody>";
      for ($i=0; $i<$swimmerCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $age = date_diff(date_create($row['DateOfBirth']),
        date_create('today'))->y;
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
        $no_parent = "";
        if ($row['UserID'] == null && $age < 18) {
          $no_parent = "<span class=\"badge badge-primary\">NO PARENT</span>";
        }
        $content .= "
        <tr>
          <td>
            <div class=\"custom-control custom-checkbox\">
            <input type=\"checkbox\" class=\"custom-control-input\" " . $checked . " name=\"Member-" . $row['MemberID'] . "\" value=\"1\" id=\"Member-" . $row['MemberID'] . "\">
            <label class=\"custom-control-label\" for=\"Member-" . $row['MemberID'] . "\">" . $row['MForename'] . " " . $row['MSurname'] . " " . $no_parent . "</label>
            </div>
          </td>
          <td>";
          if ($row['Conditions'] != "" || $row['Allergies'] != "" || $row['Medication'] != "") {
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
                  <p class="mb-0 mt-2">
                    <em>
                      Medical Conditions or Disabilities
                    </em>
                  </p>';
                  if ($row["Conditions"] != "") {
                    $modalOutput .= '
                    <p class="mb-0">';
                    $modalOutput .= $row["Conditions"];
                    $modalOutput .= '</p>';
                  } else {
                    $modalOutput .= '<p class="mb-0">None</p>';
                  }

                  $modalOutput .= '<p class="mb-0 mt-2">
                    <em>
                      Allergies
                    </em>
                  </p>';
                  if ($row["Allergies"] != "") {
                    $modalOutput .= '<p class="mb-0">';
                    $modalOutput .= $row["Allergies"];
                    $modalOutput .= '</p>';
                  } else {
                    $modalOutput .= '<p class="mb-0">None</p>';
                  }

                  $modalOutput .= '<p class="mb-0 mt-2">
                    <em>
                      Medication
                    </em>
                  </p>';
                  if ($row["Medication"] != "") {
                    $modalOutput .= '<p class="mb-0">';
                    $modalOutput .= $row["Medication"];
                    $modalOutput .= '</p>';
                  } else {
                    $modalOutput .= '<p class="mb-0">None</p>';
                  }
                  $modalOutput .= '
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
          if (($row['Website'] != 1 || $row['Social'] != 1 || $row['Noticeboard'] != 1 || $row['FilmTraining'] != 1 || $row['ProPhoto'] != 1) && ($age < 18)) {
            $content .= " <a data-toggle=\"modal\" href=\"#photoModal" . $row['MemberID'] . "\"><span class=\"badge badge-warning\">PHOTO</span></a>";
            $modalOutput .= '
            <!-- Modal -->
            <div class="modal fade" id="photoModal' . $row['MemberID'] . '" tabindex="-1" role="dialog" aria-labelledby="photoModalTitle' . $row['MemberID'] . '" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="photoModalTitle' . $row['MemberID'] . '">Photography Permissions for ' . $row['MForename'] . ' ' . $row['MSurname'] . '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  <p>There are limited photography permissions for this swimmer</p>
                  <ul>';
                  if ($row['Website'] != 1) {
                    $modalOutput .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our website</li>';
                  }
                  if ($row['Social'] != 1) {
                    $modalOutput .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our social media</li>';
                  }
                  if ($row['Noticeboard'] != 1) {
                    $modalOutput .= '<li>Photos <strong>must not</strong> be taken of this swimmer for our noticeboard</li>';
                  }
                  if ($row['FilmTraining'] != 1) {
                    $modalOutput .= '<li>This swimmer <strong>must not</strong> be filmed for the purposes of training</li>';
                  }
                  if ($row['ProPhoto'] != 1) {
                    $modalOutput .= '<li>Photos <strong>must not</strong> be taken of this swimmer by photographers</li>';
                  }
                  $modalOutput .= '
                  </ul>
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
      $content .= '</tbody></table></div><p>Swimmers with <span class="badge badge-primary">NO PARENT</span> next to their name may soon be stopped from swimming if parents do not register for an online account.</p><p class="mb-0"><button type="submit" class="btn btn-outline-dark">Save Register</button></p>';
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
