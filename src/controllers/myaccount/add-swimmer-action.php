<?php

  $preventLoginRedirect = true;
  include "../database.php";

  // Registration Form Handler

  $userID = $_SESSION['UserID'];
  $asaNumber = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['asa'])));
  $accessKey = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['accessKey'])));

  $searchSQL = "SELECT * FROM members WHERE ASANumber = '$asaNumber' AND AccessKey = '$accessKey' LIMIT 0, 30 ";
  $searchResult = mysqli_query($link, $searchSQL);
  $searchCount = mysqli_num_rows($searchResult);
  $row = mysqli_fetch_array($searchResult, MYSQLI_ASSOC);

  if ($asaNumber != null && $accessKey != null) {
    if ($searchCount == 1) {
      // Allow addition
      $memberID = $row['MemberID'];
      $squadID = $row['SquadID'];
      $existingUserID = $row['UserID'];

      if ($row['UserID'] != null) {
        $sql = "SELECT * FROM users WHERE UserID = '$existingUserID' LIMIT 0, 30 ";
        $result = mysqli_query($link, $sql);
        $oldUser = mysqli_fetch_array($searchResult, MYSQLI_ASSOC);
        // Warn old parent by email
        $message = "
        <h1>Hello " . $oldUser['Forename'] . "</h1>
        <p>Your swimmer, " . $row['MForename'] . " " . $row['MSurname'] . " has been removed
        from your account.</p>
        <p>If this was not you, contact <a href=\"mailto:support@chesterlestreetasc.co.uk\">
        support@chesterlestreetasc.co.uk</a> as soon as possible</p>";
        notifySend($oldUser['EmailAddress'], "A swimmer has been removed", $message);
      }

      $accessKey = generateRandomString(6);

      // SQL To set UserID foreign key
      $sql = "UPDATE `members` SET UserID = '$userID', AccessKey = '$accessKey' WHERE MemberID = '$memberID'";
      mysqli_query($link, $sql);

      // Get info about swimmer and parent
      $sql = "SELECT members.MemberID, members.MForename, members.MSurname, users.Forename, users.Surname, users.EmailAddress, members.ASANumber, squads.SquadName, squads.SquadFee
              FROM ((members
                INNER JOIN users ON members.UserID = users.UserID)
                INNER JOIN squads ON members.SquadID = squads.SquadID
              ) WHERE users.UserID = '$userID' AND members.MemberID = '$memberID';";
      $result = mysqli_query($link, $sql);
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

      // Notify new parent with email
      $message = "
      <p>Hello " . $row['Forename'] . " " . $row['Surname'] . ",</p>
      <p>Your swimmer, " . $row['MForename'] . " " . $row['MSurname'] . " has been registered
      with your account.</p>
      <ul>
        <li>" . $row['MForename'] . " " . $row['MSurname'] . "</li>
        <li>Squad: " . $row['SquadName'] . "</li>
        <li>Monthly Fee: &pound;" . $row['SquadFee'] . "</li>
        <li>ASA Number: " . $row['ASANumber'] . "</li>
        <li>CLS ASC Member ID: " . $row['MemberID'] . "</li>
      </ul>
      <p>If this was not you, contact <a href=\"mailto:support@chesterlestreetasc.co.uk\">
      support@chesterlestreetasc.co.uk</a> as soon as possible</p>";
      notifySend($row['EmailAddress'], "A swimmer has been added", $message);

      // Return to My Account
      header("Location: " . autoUrl(""));

    }
    else {
      // Error, too many records found - Database error
      header("Location: " . autoUrl("addswimmer"));
    }
  }
  else {
    // Error, fields not filled out
    header("Location: " . autoUrl("addswimmer");
  }
?>
