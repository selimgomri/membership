<?php
  // Connections
  include_once "../database.php";
  $userID = $_SESSION['UserID'];
  $access = $_SESSION['AccessLevel'];

  // Requested resource
  $url = mysqli_real_escape_string($link, $_SERVER['REQUEST_URI']);
  $pos = strrpos($url, '/');
  $id = $pos === false ? $url : substr($url, $pos + 1);

  function getMemberNameByID($db, $id) {
    $sql = "SELECT `MForename`, `MSurname` FROM `members` WHERE MemberID = '$id';";
    $result = mysqli_query($db, $sql);
    if ($result) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      return $row['MForename'] . " " . $row['MSurname'];
    }
  }

  function getMemberInfoByID($db, $id) {
    $sql = "SELECT members.MForename, members.MForename, members.MMiddleNames, members.MSurname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadCoach, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $output = "<div class=\"table-responsive\"><table class=\"table table-hover\">";
    $output .= "<thead><tr><th>Column</th><th>Field</th></tr></thead><tbody>";
    // Main Info Content
    $output .= "<tr><th>Forename</th><td>" . $row['MForename'] . "</td></tr>";
    if ($row['MMiddleNames'] != null) {
      $output .= "<tr><th>Middle Names</th><td>" . $row['MMiddleNames'] . "</td></tr>";
    }
    $output .= "<tr><th>Surname</th><td>" . $row['MSurname'] . "</td></tr>";
    $output .= "<tr><th>Date of Birth</th><td>" . date('j F Y', strtotime($row['DateOfBirth'])) . "</td></tr>";
    $output .= "<tr><th>ASA Number</th><td><a href=\"https://www.swimmingresults.org/biogs/biogs_details.php?tiref=" . $row['ASANumber'] . "\" target=\"_blank\" title=\"ASA Biographical Data\">" . $row['ASANumber'] . "</a></td></tr>";
    $output .= "<tr><th>Sex</th><td>" . $row['Gender'] . "</td></tr>";
    if ($row['OtherNotes'] != null) {
      $output .= "<tr><th>Notes</th><td>" . $row['OtherNotes'] . "</td></tr>";
    }
    $output .= "<tr><th>Squad</th><td>" . $row['SquadName'] . "</td></tr>";
    $output .= "<tr><th>Coach</th><td>" . $row['SquadCoach'] . "</td></tr>";
    $output .= "</tbody></table></div>";
    return $output;
  }

  // Variables for display
  $title = $content = '';

  if ($access == "Parent") {
    if ($id == "") {
      $pagetitle = "My Swimmers";
      $title = "My Swimmers";
      $content = "<p class=\"lead\">My Swimmers shows you all of your registered swimmers and allows you to easily change their details.</p>";
      $content .= "<p>Please remember that it is your responsibility to also keep the ASA Membership System up to date with personal details.</p>";
      $content .= mySwimmersTable($link, $userID);
      include "../header.php";
    }
    elseif (($id != null || $id != "")) {
      $forenameUpdate = false;
      $middlenameUpdate = false;
      $surnameUpdate = false;
      $dateOfBirthUpdate = false;
      $sexUpdate = false;
      $medicalNotesUpdate = false;
      $otherNotesUpdate = false;
      $update = false;
      $successInformation = "";

      $query = "SELECT * FROM members WHERE MemberID = '$id' ";
      $result = mysqli_query($link, $query);
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

      $forename = $row['MForename'];
      $middlename = $row['MMiddleNames'];
      $surname = $row['MSurname'];
      $dateOfBirth = $row['DateOfBirth'];
      $sex = $row['Gender'];
      $medicalNotes = $row['MedicalNotes'];
      $otherNotes = $row['OtherNotes'];

      // Get the swimmer name
      $sqlSecurityCheck = "SELECT `MForename`, `MSurname`, `UserID` FROM `members` WHERE MemberID = '$id';";
      $resultSecurityCheck = mysqli_query($link, $sqlSecurityCheck);
      $swimmersSecurityCheck = mysqli_fetch_array($resultSecurityCheck, MYSQLI_ASSOC);

      if ($swimmersSecurityCheck['UserID'] == $userID && $resultSecurityCheck) {
        if (!empty($_POST['forename'])) {
          $newForename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
          if ($newForename != $forename) {
            $sql = "UPDATE `members` SET `MForename` = '$newForename' WHERE `MemberID` = '$id'";
            mysqli_query($link, $sql);
            $forenameUpdate = true;
            $update = true;
          }
        }
        if (!empty($_POST['middlenames'])) {
          $newMiddlenames = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['middlenames']))));
          if ($newMiddlenames != $middlename) {
            $sql = "UPDATE `members` SET `MMiddleNames` = '$newMiddlenames' WHERE `MemberID` = '$id'";
            mysqli_query($link, $sql);
            $middlenameUpdate = true;
            $update = true;
          }
        }
        if (!empty($_POST['surname'])) {
          $newSurname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
          if ($newSurname != $surname) {
            $sql = "UPDATE `members` SET `MSurname` = '$newSurname' WHERE `MemberID` = '$id'";
            mysqli_query($link, $sql);
            $surnameUpdate = true;
            $update = true;
          }
        }
        if (!empty($_POST['datebirth'])) {
          $newDateOfBirth = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['datebirth']))));
          // NEEDS WORK FOR DATE TO BE RIGHT
          if ($newDateOfBirth != $dateOfBirth) {
            $sql = "UPDATE `members` SET `DateOfBirth` = '$newDateOfBirth' WHERE `MemberID` = '$id'";
            mysqli_query($link, $sql);
            $dateOfBirthUpdate = true;
            $update = true;
          }
        }
        if (!empty($_POST['sex'])) {
          $newSex = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['sex']))));
          if ($newSex != $sex) {
            $sql = "UPDATE `members` SET `Gender` = '$newSex' WHERE `MemberID` = '$id'";
            mysqli_query($link, $sql);
            $sexUpdate = true;
            $update = true;
          }
        }
        if (!empty($_POST['medicalNotes'])) {
          $newMedicalNotes = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['medicalNotes']))));
          if ($newMedicalNotes != $medicalNotes) {
            $sql = "UPDATE `members` SET `MedicalNotes` = '$newMedicalNotes' WHERE `MemberID` = '$id'";
            mysqli_query($link, $sql);
            $medicalNotesUpdate = true;
            $update = true;
          }
        }
        if (!empty($_POST['otherNotes'])) {
          $newOtherNotes = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['otherNotes']))));
          if ($newOtherNotes != $otherNotes) {
            $sql = "UPDATE `members` SET `OtherNotes` = '$newOtherNotes' WHERE `MemberID` = '$id'";
            mysqli_query($link, $sql);
            $otherNotesUpdate = true;
            $update = true;
          }
        }
      }

      $pagetitle;
      if ($swimmersSecurityCheck['UserID'] == $userID && $resultSecurityCheck) {
        $pagetitle = "Swimmer: " . $swimmersSecurityCheck['MForename'] . " " . $swimmersSecurityCheck['MSurname'];
        $sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames, members.MSurname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes FROM ((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
        $resultSwim = mysqli_query($link, $sqlSwim);
        $rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
        $title = $swimmersSecurityCheck['MForename'] . " " . $swimmersSecurityCheck['MSurname'];
        $content = "<div class=\"row\"><div class=\"col col-md-8\">";
        if ($update) {
        $content .= '<div class="alert alert-success">
          <strong>We have updated</strong>
          <ul class="mb-0">';
            if ($forenameUpdate) { $content .= '<li>Your first name</li>'; }
            if ($middlenameUpdate) { $content .= '<li>Your middle name(s)</li>'; }
            if ($surnameUpdate) { $content .= '<li>Your last address</li>'; }
            if ($dateOfBirthUpdate) { $content .= '<li>Your date of birth</li>'; }
            if ($sexUpdate) { $content .= '<li>Your sex</li>'; }
            if ($medicalNotesUpdate) { $content .= '<li>Your medical notes</li>'; }
            if ($otherNotesUpdate) { $content .= '<li>Your other notes</li>'; }
        $content .= '
          </ul>
        </div>';
        }
        // Main Info Content
        $content .= "<form method=\"post\" action=\"#\">";
        $content .= "
        <div class=\"form-group\">
          <label for=\"forename\">Forename</label>
          <input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" value=\"" . $rowSwim['MForename'] . "\" required>
        </div>";
        $content .= "
        <div class=\"form-group\">
          <label for=\"middlenames\">Middle Names</label>
          <input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\" value=\"" . $rowSwim['MMiddleNames'] . "\">
        </div>";
        $content .= "
        <div class=\"form-group\">
          <label for=\"surname\">Surname</label>
          <input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" value=\"" . $rowSwim['MSurname'] . "\" required>
        </div>";
        $content .= "
        <div class=\"form-group\">
          <label for=\"datebirth\">Date of Birth</label>
          <input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" placeholder=\"Date of Birth\" value=\"" . $rowSwim['DateOfBirth'] . "\" required>
        </div>";
        $content .= "
        <div class=\"form-group\">
          <label for=\"asaregnumber\">ASA Registration Number</label>
          <input type=\"test\" class=\"form-control\" id=\"asaregnumber\" name=\"asaregnumber\" placeholder=\"ASA Registration Numer\" value=\"" . $rowSwim['ASANumber'] . "\" readonly>
        </div>";
        /*$sql = "SELECT COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = 'chesterlestreetasc_co_uk_membership'
        AND TABLE_NAME = 'members'
        AND COLUMN_NAME = 'Gender';";
        $resultGender = mysqli_query($link, $sqlSwim);*/
        if ($rowSwim['Gender'] == "Male") {
          $content .= "
          <div class=\"form-group\">
            <label for=\"sex\">Sex</label>
            <select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
              <option value=\"Male\" selected>Male</option>
              <option value=\"Female\">Female</option>
            </select>
          </div>";
        }
        else {
          $content .= "
          <div class=\"form-group\">
            <label for=\"sex\">Sex</label>
            <select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
              <option value=\"Male\">Male</option>
              <option value=\"Female\" selected>Female</option>
            </select>
          </div>";
        }
        $content .= "
        <div class=\"form-group\">
          <label for=\"medicalNotes\">Medical Notes</label>
          <textarea class=\"form-control\" id=\"medicalNotes\" name=\"medicalNotes\" rows=\"3\" placeholder=\"Tell us about any medical issues\">" . $rowSwim['MedicalNotes'] . "</textarea>
        </div>";
        $content .= "
        <div class=\"form-group\">
          <label for=\"otherNotes\">Other Notes</label>
          <textarea class=\"form-control\" id=\"otherNotes\" name=\"otherNotes\" rows=\"3\" placeholder=\"Tell us any other notes for coaches\">" . $rowSwim['OtherNotes'] . "</textarea>
        </div>";
        $content .= "<button type=\"submit\" class=\"btn btn-success mb-3\">Update</button>";
        $content .= "</div><div class=\"col-md-4\">";
        $content .= "<div class=\"cell\"><h2>Squad Information</h2><ul class=\"mb-0\"><li>Squad: " . $rowSwim['SquadName'] . "</li><li>Monthly Fee: &pound;" . $rowSwim['SquadFee'] . "</li>";
        if ($rowSwim['SquadTimetable'] != "") {
          $content .= "<li><a href=\"" . $rowSwim['SquadTimetable'] . "\">Squad Timetable</a></li>";
        }
        if ($rowSwim['SquadCoC'] != "") {
          $content .= "<li><a href=\"" . $rowSwim['SquadCoC'] . "\">Squad Code of Conduct</a></li>";
        }
        $content .= "</ul></div>";
        $content .= "</div></div>";
      }
      else {
        // Not allowed or not found
        $pagetitle = "Error 404 - Not found";
        $title = "Error 404 - Not found";
      }

      include "../header.php";
      ?>
      <script src="<?php echo autoUrl('js/tinymce/tinymce.min.js') ?>" async defer></script>
      <script>
        tinymce.init({
          selector: '#medicalNotes',
          branding: false,
        });
      </script>
      <?php

    }
  }
  elseif ($access == "Galas") {
    // Gala Access
    if ($id == "") {
      $pagetitle = "Members";
      $title = "Member Directory";
      $content = "<p class=\"lead\">A list of members.</p>";
      $content .= adminSwimmersTable($link);
    }
    elseif (($id != null || $id != "")) {
      // Get the swimmer function
      $pagetitle = "Swimmer: " . getMemberNameByID($link, $id);
      $title = getMemberNameByID($link, $id);
      $content = getMemberInfoByID($link, $id);
    }
    else {
      // Not allowed or not found
      $pagetitle = "Error 404 - Not found";
      $title = "Error 404 - Not found";
    }

    include "../header.php";

  }
  elseif ($access == "Coach") {
    // Coaches access details about their squads
    if ($id == "") {
      $pagetitle = "Swimmers";
      $title = "Swimmer Directory";
      $content = "<p class=\"lead\">A list of swimmers in your squad(s).</p>";
      $content .= adminSwimmersTable($link, $userID);
    }
    elseif (($id != null || $id != "")) {
      // Get the swimmer function
      $pagetitle = "Swimmer: " . getMemberNameByID($link, $id);
      $title = getMemberNameByID($link, $id);
      $content = getMemberInfoByID($link, $id);
    }
    else {
      // Not allowed or not found
      $pagetitle = "Error 404 - Not found";
      $title = "Error 404 - Not found";
    }

    include "../header.php";
  }
  elseif ($access == "Committee" || $access == "Admin") {
    if ($id == "") {
      $pagetitle = "Members";
      $title = "Member Directory";
      $content = "<p class=\"lead\">A list of members.</p>";
      $content = "<p><a href=\"add-member\" class=\"btn btn-light\">Add member</a> <a href=\"accesskeys\" class=\"btn btn-light\">Access Keys</a></p>";
      $content .= adminSwimmersTable($link);
    }
    elseif ($id == "accesskeys") {
      $pagetitle = "Access Keys";
      $title = "Member Access Keys";
      $content = "<p class=\"lead\">See access keys.</p>";
      include "accesskeys.php";
    }
    elseif ($id == "add-member") {
      $pagetitle = "Add a member";
      $title = "Add a member";
      $content = "<p class=\"lead\">Add a member to the club system.</p>";
      $added = false;

      $forename = $middlenames = $surname = $dateOfBirth = $asaNumber = $sex = $squad = $sql = "";

      if ((!empty($_POST['forename']))  && (!empty($_POST['surname'])) && (!empty($_POST['datebirth'])) && (!empty($_POST['sex'])) && (!empty($_POST['squad']))) {
        $forename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
        $surname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
        $dateOfBirth = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['datebirth'])));
        $sex = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['sex'])));
        $squad = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['squad'])));
        if ((!empty($_POST['middlenames']))) {
          $middlenames = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['middlenames']))));
        }
        if ((!empty($_POST['asa']))) {
          $asaNumber = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['asa'])));
        }

        $accessKey = generateRandomString(6);

        $sql = "INSERT INTO `members` (`MemberID`, `MForename`, `MMiddleNames`, `MSurname`, `DateOfBirth`, `ASANumber`, `Gender`, `SquadID`, `AccessKey`) VALUES (NULL, '$forename', '$middlenames', '$surname', '$dateOfBirth', '$asaNumber', '$sex', '$squad', '$accessKey');";
        $action = mysqli_query($link, $sql);
        if ($action) {
          $added = true;
        }
      }

      $content = "<div class=\"row\"><div class=\"col col-md-8\">";
      if ($added) {
      $content .= '<div class="alert alert-success">
        <strong>We added the member</strong>';
      $content .= '</div>';
      }
      // Main Info Content
      $content .= "<form method=\"post\" action=\"#\">";
      $content .= "
      <div class=\"form-group\">
        <label for=\"forename\">Forename</label>
        <input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"middlenames\">Middle Names</label>
        <input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\">
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"surname\">Surname</label>
        <input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"datebirth\">Date of Birth</label>
        <input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" placeholder=\"Date of Birth\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"asa\">ASA Registration Number</label>
        <input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\" placeholder=\"ASA Registration Numer\">
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"sex\">Sex</label>
        <select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
          <option value=\"Male\">Male</option>
          <option value=\"Female\">Female</option>
        </select>
      </div>";
      $sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
      $result = mysqli_query($link, $sql);
      $squadCount = mysqli_num_rows($result);
      $content .= "
      <div class=\"form-group\">
        <label for=\"squad\">Squad</label>
          <select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
      //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      for ($i = 0; $i < $squadCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "<option value=\"" . $row['SquadID'] . "\"";
        $content .= ">" . $row['SquadName'] . "</option>";
      }
      $content .= "</select></div>";
      $content .= "<button type=\"submit\" class=\"btn btn-success mb-3\">Add Member</button>";
      $content .= "</div><div class=\"col-md-4\">";
      $content .= "</div></div>";
    }
    elseif (($id != null || $id != "")) {
      // Committee or Admin can see and change all data
      $forenameUpdate = false;
      $middlenameUpdate = false;
      $surnameUpdate = false;
      $asaUpdate = false;
      $userUpdate = false;
      $squadUpdate = false;
      $dateOfBirthUpdate = false;
      $sexUpdate = false;
      $medicalNotesUpdate = false;
      $otherNotesUpdate = false;
      $update = false;
      $successInformation = "";

      $query = "SELECT * FROM members WHERE MemberID = '$id' ";
      $result = mysqli_query($link, $query);
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

      $forename = $row['MForename'];
      $middlename = $row['MMiddleNames'];
      $surname = $row['MSurname'];
      $asaNumber = $row['ASANumber'];
      $dbUserID = "";
      $dbUserID = $row['UserID'];
      $squad = $row['SquadID'];
      $dateOfBirth = $row['DateOfBirth'];
      $sex = $row['Gender'];
      $medicalNotes = $row['MedicalNotes'];
      $otherNotes = $row['OtherNotes'];

      if (!empty($_POST['forename'])) {
        $newForename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
        if ($newForename != $forename) {
          $sql = "UPDATE `members` SET `MForename` = '$newForename' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $forenameUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['middlenames'])) {
        $newMiddlenames = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['middlenames']))));
        if ($newMiddlenames != $middlename) {
          $sql = "UPDATE `members` SET `MMiddleNames` = '$newMiddlenames' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $middlenameUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['surname'])) {
        $newSurname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
        if ($newSurname != $surname) {
          $sql = "UPDATE `members` SET `MSurname` = '$newSurname' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $surnameUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['asa'])) {
        $newASANumber = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['asa']))));
        if ($newASANumber != $asaNumber) {
          $sql = "UPDATE `members` SET `ASANumber` = '$newASANumber' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $asaUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['userid'])) {
        $newUserID = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['userid']))));
        if ($newUserID != $dbUserID) {
          $sql = "UPDATE `members` SET `UserID` = '$newUserID' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $userUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['squad'])) {
        $newSquadID = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['squad']))));
        if ($newSquadID != $squad) {
          $sql = "UPDATE `members` SET `SquadID` = '$newSquadID' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $squadUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['datebirth'])) {
        $newDateOfBirth = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['datebirth']))));
        // NEEDS WORK FOR DATE TO BE RIGHT
        if ($newDateOfBirth != $dateOfBirth) {
          $sql = "UPDATE `members` SET `DateOfBirth` = '$newDateOfBirth' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $dateOfBirthUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['sex'])) {
        $newSex = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['sex']))));
        if ($newSex != $sex) {
          $sql = "UPDATE `members` SET `Gender` = '$newSex' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $sexUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['medicalNotes'])) {
        $newMedicalNotes = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['medicalNotes']))));
        if ($newMedicalNotes != $medicalNotes) {
          $sql = "UPDATE `members` SET `MedicalNotes` = '$newMedicalNotes' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $medicalNotesUpdate = true;
          $update = true;
        }
      }
      if (!empty($_POST['otherNotes'])) {
        $newOtherNotes = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['otherNotes']))));
        if ($newOtherNotes != $otherNotes) {
          $sql = "UPDATE `members` SET `OtherNotes` = '$newOtherNotes' WHERE `MemberID` = '$id'";
          mysqli_query($link, $sql);
          $otherNotesUpdate = true;
          $update = true;
        }
      }

      $sqlSwim = "";
      $sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames, members.MSurname, members.ASANumber, squads.SquadName, squads.SquadID, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender, members.MedicalNotes, members.OtherNotes FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.MemberID = '$id';";
      $resultSwim = mysqli_query($link, $sqlSwim);
      $rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
      $pagetitle = "Swimmer: " . $rowSwim['MForename'] . " " . $rowSwim['MSurname'];
      $title = $rowSwim['MForename'] . " " . $rowSwim['MSurname'];
      $content = "<div class=\"row\"><div class=\"col col-md-8\">";
      if ($update) {
      $content .= '<div class="alert alert-success">
        <strong>We have updated</strong>
        <ul class="mb-0">';
          if ($forenameUpdate) { $content .= '<li>First name</li>'; }
          if ($middlenameUpdate) { $content .= '<li>Middle name(s)</li>'; }
          if ($surnameUpdate) { $content .= '<li>Last address</li>'; }
          if ($dateOfBirthUpdate) { $content .= '<li>Date of birth</li>'; }
          if ($asaUpdate) { $content .= '<li>ASA Number</li>'; }
          if ($userUpdate) { $content .= '<li>Parent</li>'; }
          if ($squadUpdate) { $content .= '<li>Squad</li>'; }
          if ($sexUpdate) { $content .= '<li>Sex</li>'; }
          if ($medicalNotesUpdate) { $content .= '<li>Medical notes</li>'; }
          if ($otherNotesUpdate) { $content .= '<li>Other notes</li>'; }
      $content .= '
        </ul>
      </div>';
      }
      // Main Info Content
      $content .= "<form method=\"post\" action=\"#\">";
      $content .= "
      <div class=\"form-group\">
        <label for=\"forename\">Forename</label>
        <input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" value=\"" . $rowSwim['MForename'] . "\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"middlenames\">Middle Names</label>
        <input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\" value=\"" . $rowSwim['MMiddleNames'] . "\">
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"surname\">Surname</label>
        <input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" value=\"" . $rowSwim['MSurname'] . "\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"datebirth\">Date of Birth</label>
        <input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" placeholder=\"Date of Birth\" value=\"" . $rowSwim['DateOfBirth'] . "\" required>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"asa\">ASA Registration Number</label>
        <input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\" placeholder=\"ASA Registration Numer\" value=\"" . $rowSwim['ASANumber'] . "\">
      </div>";
      /*$sql = "SELECT COLUMN_TYPE
      FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = 'chesterlestreetasc_co_uk_membership'
      AND TABLE_NAME = 'members'
      AND COLUMN_NAME = 'Gender';";
      $resultGender = mysqli_query($link, $sqlSwim);*/
      if ($rowSwim['Gender'] == "Male") {
        $content .= "
        <div class=\"form-group\">
          <label for=\"sex\">Sex</label>
          <select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
            <option value=\"Male\" selected>Male</option>
            <option value=\"Female\">Female</option>
          </select>
        </div>";
      }
      else {
        $content .= "
        <div class=\"form-group\">
          <label for=\"sex\">Sex</label>
          <select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
            <option value=\"Male\">Male</option>
            <option value=\"Female\" selected>Female</option>
          </select>
        </div>";
      }
      $sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
      $result = mysqli_query($link, $sql);
      $squadCount = mysqli_num_rows($result);
      $content .= "
      <div class=\"form-group\">
        <label for=\"squad\">Squad</label>
          <select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
      //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      for ($i = 0; $i < $squadCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "<option value=\"" . $row['SquadID'] . "\"";
        if ($row['SquadID'] == $rowSwim['SquadID']) {
          $content .= " selected";
        }
        $content .= ">" . $row['SquadName'] . "</option>";
      }
      $content .= "</select></div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"medicalNotes\">Medical Notes</label>
        <textarea class=\"form-control\" id=\"medicalNotes\" name=\"medicalNotes\" rows=\"3\" placeholder=\"Tell us about any medical issues\">" . $rowSwim['MedicalNotes'] . "</textarea>
      </div>";
      $content .= "
      <div class=\"form-group\">
        <label for=\"otherNotes\">Other Notes</label>
        <textarea class=\"form-control\" id=\"otherNotes\" name=\"otherNotes\" rows=\"3\" placeholder=\"Tell us any other notes for coaches\">" . $rowSwim['OtherNotes'] . "</textarea>
      </div>";
      $content .= "<button type=\"submit\" class=\"btn btn-success mb-3\">Update</button>";
      $content .= "</div><div class=\"col-md-4\">";
      $content .= "<div class=\"cell\"><h2>Squad Information</h2><ul class=\"mb-0\"><li>Squad: " . $rowSwim['SquadName'] . "</li><li>Monthly Fee: &pound;" . $rowSwim['SquadFee'] . "</li>";
      if ($rowSwim['SquadTimetable'] != "") {
        $content .= "<li><a href=\"" . $rowSwim['SquadTimetable'] . "\">Squad Timetable</a></li>";
      }
      if ($rowSwim['SquadCoC'] != "") {
        $content .= "<li><a href=\"" . $rowSwim['SquadCoC'] . "\">Squad Code of Conduct</a></li>";
      }
      $content .= "</ul></div>";
      $content .= "</div></div>";
    }
    else {
      // Not allowed or not found
      $pagetitle = "Error 404 - Not found";
      $title = "Error 404 - Not found";
    }

    include "../header.php";

  }
  else {
    // Error
  }

?>
<div class="container">
  <h1><?php echo $title ?></h1>
  <div><?php echo $content ?></div>
</div>
<?php

  include "../footer.php";
?>
