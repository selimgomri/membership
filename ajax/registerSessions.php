<?php
include_once "../database.php";
$access = $_SESSION['AccessLevel'];
$swimmerCount = 0;

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
  if (isset($_REQUEST["squadID"])) {
    // get the squadID parameter from URL
    $squadID = mysqli_real_escape_string($link, $_REQUEST["squadID"]);

    $response = "";

    if ($squadID != null) {
      $sql = "SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE squads.SquadID = '$squadID';";
      $result = mysqli_query($link, $sql);
      $swimmerCount = mysqli_num_rows($result);
      $content = '<option>Choose the session from the menu</option>';
      for ($i=0; $i<$swimmerCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "<option value=\"" . $row['SessionID'] . "\">" . $row['SessionDay'] . " " . $row['SessionName'] . "</option>";
      }
    }

    if ($swimmerCount > 0) {
      echo $content;
    }
    else {
      echo "<option selected>Couldn't find anything</option>";
    }
  }

  if (isset($_REQUEST["sessionID"])) {

    $sessionID = mysqli_real_escape_string($link, $_REQUEST["sessionID"]);
    $response = $content = "";

    if ($sessionID != null) {
      $sql = "SELECT * FROM (sessions INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE sessions.SessionID = '$sessionID';";
      $result = mysqli_query($link, $sql);
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $content .= "<h2>Take register for " . $row['SquadName'] . " Squad, " . $row['SessionDay'] . " " . $row['SessionName'] . "</h2>";
      $sql = "SELECT * FROM ((sessions INNER JOIN members ON sessions.SquadID = members.SquadID) INNER JOIN squads ON sessions.SquadID = squads.SquadID) WHERE sessions.SessionID = '$sessionID'";
      $result = mysqli_query($link, $sql);
      $swimmerCount = mysqli_num_rows($result);
      $content .= "<div class=\"table-responsive\"><table class=\"table table-striped\"><thead><tr><th>Name</th><th>Notes</th></tr></thead><tbody>";
      for ($i=0; $i<$swimmerCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "
        <tr>
          <td>
            <div class=\"custom-control custom-checkbox\">
            <input type=\"checkbox\" class=\"custom-control-input\" name=\"Member-" . $row['MemberID'] . "\" value=\"1\" id=\"Member-" . $row['MemberID'] . "\">
            <label class=\"custom-control-label\" for=\"Member-" . $row['MemberID'] . "\">" . $row['MForename'] . " " . $row['MSurname'] . "</label>
            </div>
          </td>
          <td>";
          if ($row['MedicalNotes'] != "") {
            $content .= "<span class=\"badge badge-danger\">MEDICAL</span>";
          }
          if ($row['OtherNotes'] != "") {
            $content .= " <span class=\"badge badge-info\">OTHER</span>";
          }
          $content .= "
          </td>
        </tr>";
      }
      $content .= '</tbody></table></div><p><button type="submit" class="btn btn-success">Save Register</button></p>';
    }

    if ($swimmerCount > 0) {
      echo $content;
    }
    else {
      echo "<option selected>Couldn't find anything</option>";
    }
  }

}
?>
