<?php

use Respect\Validation\Validator as v;
global $db;

$getMemberAttendance = $db->prepare("SELECT AttendanceBoolean FROM `sessionsAttendance` WHERE `WeekID` = ? AND `SessionID` = ? AND `MemberID` = ?");

$markdown = new ParsedownExtra();
$markdown->setSafeMode(true);

$swimmerCount = 0;

if ($_SESSION['AccessLevel'] == "Committee" || $_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Coach") {
  if ((isset($_REQUEST["squadID"]) && v::intVal()->validate($_REQUEST["squadID"])) || isset($preload) && $preload && $getSessions) {
    // get the squadID parameter from URL
    $squadID = $session = null;
    if (isset($preload) && $preload) {
      $squadID = $squad_init;
      $session = $session_init;
    } else {
      $squadID = $_REQUEST["squadID"];
      $session = $_REQUEST['selected'];
    }

    $response = "";

    if ($squadID != null) {
      $getSessions = $db->prepare("SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE squads.SquadID = ? AND ((sessions.DisplayFrom <= CURDATE( )) AND (sessions.DisplayUntil >= CURDATE( ))) ORDER BY sessions.SessionDay ASC, sessions.StartTime ASC");
      $getSessions->execute([$squadID]);
      $content = '<option>Choose the session from the menu</option>';

      $exists = false;

      while ($row = $getSessions->fetch(PDO::FETCH_ASSOC)) {
        $exists = true;
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

        $selected = "";
        if ($session == (int) $row['SessionID']) {
          $selected = " selected ";
        }
        $content .= "<option value=\"" . $row['SessionID'] . "\" " . $selected . ">" . htmlspecialchars($row['SessionName']) . ", " . $dayText . " at " . (new DateTime($row['StartTime']))->format("H:i") . "</option>";
      }
    }

    if ($exists) {
      echo $content;
    }
    else {
      echo "<option selected value=\"0\">No sessions to show</option>";
    }
  }

  if ((isset($_REQUEST["sessionID"]) && v::intVal()->validate($_REQUEST["sessionID"])) || isset($preload) && $preload && $getRegister) {
    if (isset($_REQUEST["date"]) && v::intVal()->validate($_REQUEST["date"])) {
      $dateO = $date = $_REQUEST["date"];
    }

    $sessionID = null;

    if (isset($preload) && $preload) {
      $sessionID = $session_init;
      $dateO = $date = $weekID;
    } else {
      $sessionID = $_REQUEST["sessionID"];
    }

    $response = $content = $modalOutput = "";

    if ($sessionID != null) {
      // Check if the register has been done before
      $sessionRecord = $db->prepare("SELECT COUNT(*) FROM `sessionsAttendance` WHERE `WeekID` = ? AND `SessionID` = ?");
      $sessionRecord->execute([$date, $sessionID]);
      $sessionRecordExists = $sessionRecord->fetchColumn();

      $getWeekBeginning = $db->prepare("SELECT `WeekDateBeginning` FROM `sessionsWeek` WHERE `WeekID` = ?");
      $getWeekBeginning->execute([$date]);
      $weekBeginning = $getWeekBeginning->fetchColumn();

      $sessionDetails = $db->prepare("SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE sessions.SessionID = ?");
      $sessionDetails->execute([$sessionID]);
      $row = $sessionDetails->fetch(PDO::FETCH_ASSOC);
      $dayAdd = $row['SessionDay'];
      $sessionDate = strtotime($weekBeginning. ' + ' . $dayAdd . ' days');
      $date = date ('j F Y', $sessionDate);

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
      $content .= "<h2>Take register</h2><p>for " . htmlspecialchars($row['SquadName']) . " Squad, " . htmlspecialchars($row['SessionName']) . " on " . $dayText . " " . $date . " at " . $datetime1->format("H:i") . "</p>";
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
      memberMedical.MemberID) WHERE sessions.SessionID = ? AND members.Status = '1' ORDER BY
      members.MForename, members.MSurname ASC";
      $swimmers = $db->prepare($sql);
      $swimmers->execute([$sessionID]);

      $content .= "<div class=\"table-responsive-md table-nomargin mb-1\">";
      if (app('request')->isMobile()) {
        $content .= '<table class="table">';
      } else {
        $content .= '<table class="table">';
      }
      $content .= "<thead class=\"thead-light\"><tr><th>Swimmer</th><th>Notes</th></tr></thead><tbody>";
      while ($row = $swimmers->fetch(PDO::FETCH_ASSOC)) {
        $swimmerCount += 1;
        $age = date_diff(date_create($row['DateOfBirth']), date_create('today'))->y;
        $ageOnSession = date_diff(date_create($row['DateOfBirth']), date_create($weekBeginning. ' + ' . $dayAdd . ' days'))->y;
        $checked = "";
        if ($sessionRecordExists > 0) {
          $member = $row['MemberID'];
          $getMemberAttendance->execute([$dateO, $sessionID, $row['MemberID']]);
          $column = $getMemberAttendance->fetchColumn();

          if ($column != null && $column) {
            $checked = "checked";
          }
        }
        $no_parent = "";
        if (date("m-d", strtotime($row['DateOfBirth'])) == date("m-d", $sessionDate)) {
          $no_parent .= '<span class="sr-only"><em>Birthday is today</em></span><span class="badge badge-success"><i class="fa fa-birthday-cake" aria-hidden="true"></i> ' . $ageOnSession . ' today</span>';
        }
        if ($row['UserID'] == null && $age < 18) {
          $no_parent .= "<span class=\"badge badge-primary\">NO PARENT</span>";
        }
        $content .= "
        <tr>
          <td>
            <div class=\"custom-control custom-checkbox\">
            <input type=\"checkbox\" class=\"custom-control-input\" " . $checked . " name=\"Member-" . $row['MemberID'] . "\" value=\"1\" id=\"Member-" . $row['MemberID'] . "\">
            <label class=\"custom-control-label\" for=\"Member-" . $row['MemberID'] . "\">" . htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . " " . $no_parent . "</label>
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
                    <h3 class="modal-title" id="medicalModalTitle' . $row['MemberID'] . '">Medical Information for ' . $row['MForename'] . ' ' . $row['MSurname'] . '</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  <h4>
                    Medical Conditions or Disabilities
                  </h4>';
                  if ($row["Conditions"] != "") {
                    $modalOutput .= $markdown->text($row["Conditions"]);
                  } else {
                    $modalOutput .= '<p>None</p>';
                  }

                  $modalOutput .= '
                  <h4>
                    Allergies
                  </h4>';
                  if ($row["Allergies"] != "") {
                    $modalOutput .= $markdown->text($row["Allergies"]);
                  } else {
                    $modalOutput .= '<p>None</p>';
                  }

                  $modalOutput .= '
                  <h4>
                    Medication
                  </h4>';
                  if ($row["Medication"] != "") {
                    $modalOutput .= $markdown->text($row["Medication"]);
                  } else {
                    $modalOutput .= '<p>None</p>';
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
      $content .= '</tbody></table></div>
      <!--<p>
        <button class="btn btn-warning" type="button" data-toggle="collapse" data-target="#medicine-A-Z" aria-expanded="false" aria-controls="medicine-A-Z">
          Show/Hide Medicines A-Z
        </button>
      </p>
      <div class="collapse" id="medicine-A-Z">
        <iframe src="https://api-bridge.azurewebsites.net/medicines/?uid=Y2hyaXMuaGVwcGVsbEBjaGVzdGVybGVzdHJlZXRhc2MuY28udWs=" style="border: none; height: 450px; width: 100%;" class="mb-3"></iframe>
      </div>-->
      <p class="mb-0"><button type="submit" class="btn btn-success">Save Register</button></p>';
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
