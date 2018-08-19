<?php
$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
$entriesArray = [];
$entryID;
$added = false;
$content = "";
$counter = 0;
$entryCount = -1;

if (!empty($_POST['entryID'])) {
  $entryID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['entryID'])));
}

if (isset($entryID)) {
  // Check the user is the parent or has admin rights
  $sql = "SELECT `UserID` FROM `members` INNER JOIN galaEntries ON members.MemberID = galaEntries.MemberID WHERE EntryID = '$entryID';";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $entryUser = $row['UserID'];
  $access = $_SESSION['AccessLevel'];
  if ($entryUser != $_SESSION['UserID'] && !($access == "Galas" || $access == "Committee" || $access == "Admin")) {
    halt(403);
  }

  for ($i=0; $i<sizeof($swimsArray); $i++) {
    if (!empty($_POST[$swimsArray[$i]])) {
      $entriesArray[$i] = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST[$swimsArray[$i]])));
      $counter++;
    }
    else {
        $entriesArray[$i] = 0;
    }
  }
  $set = "";

  $fee = 0;
  $feeCheck = "SELECT `GalaFee`, `GalaFeeConstant` FROM `galas` INNER JOIN
  `galaEntries` ON galas.GalaID = galaEntries.GalaID WHERE `EntryID` =
  '$entryID';";
  $feeCheck = mysqli_query($link, $feeCheck);
  $feeCheck = mysqli_fetch_array($feeCheck, MYSQLI_ASSOC);

  if (!$feeCheck['GalaFeeConstant']) {
    $swimsArray[] = "FeeToPay";
    $fee = mysqli_real_escape_string($link, number_format($_POST['galaFee'],2,'.',''));
    $entriesArray[] = $fee;
  } else {
    $swimsArray[] = "FeeToPay";
    $fee = mysqli_real_escape_string($link, number_format($feeCheck['GalaFee']*$counter,2,'.',''));
    $entriesArray[] = $fee;
  }

  for ($i=0; $i<sizeof($swimsArray); $i++) {
    if ($i < (sizeof($swimsArray)-1)) {
      $set .= "" . $swimsArray[$i] . " = '" . $entriesArray[$i] . "', ";
    }
    else {
      $set .= "" . $swimsArray[$i] . " = '" . $entriesArray[$i] . "' ";
    }
  }

  $sql = "UPDATE `galaEntries` SET $set WHERE EntryID = '$entryID';";
  $action = mysqli_query($link, $sql);
  if ($action) {
    $added = true;
  }

  $entryList = "";
  for ($i=0; $i<sizeof($swimsArray); $i++) {
    if ($entriesArray[$i] == 1) {
      $entryList .= "<li>" . $swimsTextArray[$i] . "</li>";
    }
  }

  $set = "";
  for ($i=0; $i<sizeof($swimsTimeArray); $i++) {
    if ($i < (sizeof($swimsTimeArray)-1)) {
      $set .= "`" . $swimsArray[$i] . "` ";
      $set .= " = '" . $entriesArray[$i] . "', ";
      $set .= "`" . $swimsTimeArray[$i] . "` ";
      $set .= ' = \'' . $entriesTimeArray[$i] . '\', ';
    }
    else {
      $set .= "`" . $swimsArray[$i] . "` ";
      $set .= " = '" . $entriesArray[$i] . "', ";
      $set .= "`" . $swimsTimeArray[$i] . "` ";
      $set .= " = '" . $entriesTimeArray[$i] . "' ";
    }
  }

    $sql = "UPDATE `galaEntries` SET $set WHERE EntryID = '$entryID';";
    $action = mysqli_query($link, $sql);
    if ($action) {
      $added = true;
    }

    $entryList = "";
    for ($i=0; $i<sizeof($swimsArray); $i++) {
      if ($entriesArray[$i] == 1) {
        $entryList .= "<li>" . $swimsTextArray[$i] . "</li>";
      }
    }

  }

if ($added) {

  $sql = "SELECT members.MForename, members.MSurname, galaEntries.EntryID, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.EntryID = '$entryID';";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $pagetitle = $title = "Enter a Gala";
  $content .= "<p class=\"lead\">You have successfully updated " . $row['MForename'] . " " . $row['MSurname'] . "'s entry into " . $row['GalaName'] . ".</p>";
  $content .= "<ul>" . $entryList . "</ul>";
  $content .= "<p><a class=\"btn btn-outline-dark\" href=\"" . autoUrl("galas/entries/" . $row['EntryID'] . "") . "\">Return to entry</a></p>";
  $to = $row['Forename'] . " " . $row['Surname'] . "<" . $row['EmailAddress'] . ">";
  $subject = "Your Updated " . $row['GalaName'] . " Entry";
  $message .= "<p>Here are the details of your updated Gala Entry for " . $row['MForename'] . " " . $row['MSurname'] . " to the " . $row['GalaName'] . ".</p>";
  $message .= "<ul>" . $entryList . "</ul>";
  if ($row['GalaFeeConstant'] == 1) {
    $content .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . number_format(($counter*$row['GalaFee']),2,'.','') . "</strong></p>";
    $message .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . number_format(($counter*$row['GalaFee']),2,'.','') . "</strong></p>";
  } else {
    $content .= "<p>The <strong>total fee payable is &pound;" . $fee . "</strong>. If you have entered this amount incorrectly, you may incur extra charges from the club or gala host.</p>";
    $message .= "<p>The <strong>total fee payable is &pound;" . $fee . "</strong>. If you have entered this amount incorrectly, you may incur extra charges from the club or gala host.</p>";
  }
  $notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
  `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 1, 'Galas')";
  try {
    global $db;
    $db->prepare($notify)->execute([$_SESSION['UserID'], $subject, $message]);
  } catch (PDOException $e) {
    halt(500);
  }
  //notifySend($to, $subject, $message, $row['Forename'] . " " . $row['Surname'], $row['EmailAddress']);
}
else {
  $pagetitle = $title = "An error occurred";
  $content = "<div class=\"alert alert-warning\"><strong>An error occurred</strong> <br>We could not add your entry.</div>";
}
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
?>
