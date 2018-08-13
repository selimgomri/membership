<?php

use Respect\Validation\Validator as v;

  // Registration Form Handler

  $userID = $_SESSION['UserID'];
  $asaNumber = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['asa'])));
  $accessKey = mysqli_real_escape_string($link, strtoupper(trim(htmlspecialchars($_POST['accessKey']))));

  $searchSQL = "SELECT * FROM members WHERE ASANumber = '$asaNumber' AND AccessKey = '$accessKey' LIMIT 1;";
  $searchResult = mysqli_query($link, $searchSQL);
  $searchCount = mysqli_num_rows($searchResult);
  $row = mysqli_fetch_array($searchResult, MYSQLI_ASSOC);

  if ($asaNumber != null && $accessKey != null && v::alnum()->validate($asaNumber) && v::alnum()->validate($accessKey)) {
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
        notifySend("", "A swimmer has been removed", $message,
        $oldUser['Forename'] . " " . $oldUser['Surname'],
        $oldUser['EmailAddress']);
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
      notifySend($row['EmailAddress'], "You've added " . $row['MForename'] . "
      to your account", $message, $row['Forename'] . " " . $row['Surname'],
      $row['EmailAddress']);

      $_SESSION['AddSwimmerSuccessState'] = "
      <div class=\"alert alert-succcess\">
      <p class=\"mb-0\"><strong>We were able to successfully add your swimmer</strong></p>
      <p>We've sent an email confirming this to you.</p>
      <p class=\"mb-0\"><a href=\"" . autoUrl("myaccount/addswimmer") . "\"
      class=\"alert-link\">Add another</a> or <a href=\"" . autoUrl("myaccount") . "\"
      class=\"alert-link\">return to My Account</a></p>
      </div>";
      header("Location: " . autoUrl("myaccount/addswimmer"));

      // Return to My Account
      header("Location: " . autoUrl("myaccount"));

    }
    else {
      // Error, too many records found - Database error
      $_SESSION['ErrorState'] = "
    	<div class=\"alert alert-danger\">
    	<p class=\"mb-0\"><strong>An error occured when we tried to add a swimmer</strong></p>
      <p>You may have got the ASA Number or Access Key wrong</p>
    	<p class=\"mb-0\">Please try again</p>
    	</div>";
      header("Location: " . autoUrl("myaccount/addswimmer"));
    }
  }
  else {
    // Error, fields not filled out
    $_SESSION['ErrorState'] = "
    <div class=\"alert alert-danger\">
    <p class=\"mb-0\"><strong>An error occured when we tried to add a swimmer</strong></p>
    <p>You may have got the ASA Number or Access Key wrong</p>
    <p class=\"mb-0\">Please try again</p>
    </div>";
    header("Location: " . autoUrl("myaccount/addswimmer"));
  }
?>
