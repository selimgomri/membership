<?php
$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
$entriesArray = [];
$memberID = "";
$galaID = "";
$timesRequired = "";
$added = false;
$content = "";
$counter = 0;
$entryCount = -1;

if (!empty($_POST['swimmer'])) {
  $memberID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['swimmer'])));
}

if (!empty($_POST['gala'])) {
  $galaID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['gala'])));
}

if (!empty($_POST['TimesRequired'])) {
  $timesRequired = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['TimesRequired'])));
}

if ($memberID != "" && $galaID != "") {
  $sql = "SELECT EntryID FROM galaEntries WHERE `GalaID` = '$galaID' AND `MemberID` = '$memberID';";
  $result = mysqli_query($link, $sql);
  $entryCount = mysqli_num_rows($result);
}

if ($timesRequired != 1) {
  for ($i=0; $i<sizeof($swimsArray); $i++) {
    if (!empty($_POST[$swimsArray[$i]])) {
      $entriesArray[$i] = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST[$swimsArray[$i]])));
      $counter++;
    }
    else {
        $entriesArray[$i] = 0;
    }
  }

  $swims = "";
  for ($i=0; $i<sizeof($swimsArray); $i++) {
    if ($i < (sizeof($swimsArray)-1)) {
      $swims .= "`" . $swimsArray[$i] . "`, ";
    }
    else {
      $swims .= "`" . $swimsArray[$i] . "` ";
    }
  }

  $values = "";
  for ($i=0; $i<sizeof($entriesArray); $i++) {
    if ($i < (sizeof($entriesArray)-1)) {
      $values .= "'" . $entriesArray[$i] . "', ";
    }
    else {
      $values .= "'" . $entriesArray[$i] . "' ";
    }
  }
}
elseif ($timesRequired == 1) {
  for ($i=0; $i<sizeof($swimsArray); $i++) {
    if ((!empty($_POST[$swimsTimeArray[$i] . "Mins"])) || (!empty($_POST[$swimsTimeArray[$i] . "Secs"])) || (!empty($_POST[$swimsTimeArray[$i] . "Hunds"]))) {
      $mins = mysqli_real_escape_string($link, sprintf('%02d',trim(htmlspecialchars($_POST[$swimsTimeArray[$i] . "Mins"]))));
      $secs = mysqli_real_escape_string($link, sprintf('%02d',trim(htmlspecialchars($_POST[$swimsTimeArray[$i] . "Secs"]))));
      $hunds = mysqli_real_escape_string($link, sprintf('%02d',trim(htmlspecialchars($_POST[$swimsTimeArray[$i] . "Hunds"]))));
      $entriesArray[$i] = $mins . ":" . $secs . "." . $hunds;
      $counter++;
    }
    else {
        $entriesArray[$i] = null;
    }
  }

  $swims = "";
  for ($i=0; $i<sizeof($swimsTimeArray); $i++) {
    if ($i < (sizeof($swimsTimeArray)-1)) {
      $swims .= "`" . $swimsArray[$i] . "`, ";
      $swims .= "`" . $swimsTimeArray[$i] . "`, ";
    }
    else {
      $swims .= "`" . $swimsArray[$i] . "`, ";
      $swims .= "`" . $swimsTimeArray[$i] . "` ";
    }
  }

  $values = "";
  for ($i=0; $i<sizeof($entriesArray); $i++) {
    if ($i < (sizeof($entriesArray)-1)) {
      if ($entriesArray[$i]!=null) {
        $values .= "'1', ";
      }
      else {
        $values .= "'0', ";
      }
      $values .= "'" . $entriesArray[$i] . "', ";
    }
    else {
      if ($entriesArray[$i]!=null) {
        $values .= "'1', ";
      }
      else {
        $values .= "'0', ";
      }
      $values .= "'" . $entriesArray[$i] . "'";
    }
  }
}

if ($entryCount == 0) {
  $sql = "INSERT INTO `galaEntries` (`MemberID`, `GalaID`, " . $swims . ", `TimesRequired`) VALUES ('$memberID', '$galaID', " . $values . ", '$timesRequired');";
  $action = mysqli_query($link, $sql);
  if ($action) {
    $added = true;
  }

  $entryList = "";
  $sql = "SELECT * FROM (galaEntries INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galaEntries.MemberID = '$memberID' AND galaEntries.GalaID = '$galaID';";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  // Print <li>Swim Name</li> for each entry
  for ($y=0; $y<sizeof($swimsArray); $y++) {
    if ($row[$swimsArray[$y]] == 1) {
      $entryList .= "<li>" . $swimsTextArray[$y] . "</li>";
    }
  }

  $sql = "SELECT members.MForename, members.MSurname, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.MemberID = '$memberID' and galaEntries.GalaID = '$galaID';";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $pagetitle = $title = "Enter a Gala";
  if ($added) {
    $content .= "<p class=\"lead\">You have successfully entered " . $row['MForename'] . " " . $row['MSurname'] . " into " . $row['GalaName'] . ".</p>";
    $content .= "<ul>" . $entryList . "</ul>";
    $content .= "<p><a class=\"btn btn-success\" href=\"" . autoUrl("galas/") . "\">Return to Galas</a></p>";
    $to = $row['Forename'] . " " . $row['Surname'] . "<" . $row['EmailAddress'] . ">";
    $subject = "Your Gala Entry";
    $message = "<h1>Hello " . $row['Forename'] . " " . $row['Surname'] . "</h1>";
    $message .= "<p>Here's the details of your Gala Entry for " . $row['MForename'] . " " . $row['MSurname'] . " to the " . $row['GalaName'] . ".</p>";
    $message .= "<ul>" . $entryList . "</ul>";
    if ($row['GalaFeeConstant'] == 1) {
      $content .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . number_format(($counter*$row['GalaFee']),2,'.','') . "</strong></p>";
      $message .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . number_format(($counter*$row['GalaFee']),2,'.','') . "</strong></p>";
    }
    notifySend($to, $subject, $message);
  }
}
elseif ($entryCount > 0) {
  $pagetitle = $title = "You've already entered";
  $content = "<div class=\"alert alert-warning\"><strong>You've already entered this gala</strong> <br>Please edit your entry if it has not already been processed. If it has been processed, you must talk to the Gala Coordinator.</div>";
}
else {
  $pagetitle = $title = "An error occurred";
  $content = "<div class=\"alert alert-warning\"><strong>An error occurred</strong> <br>We could not add your entry.</div>";
}
?>
