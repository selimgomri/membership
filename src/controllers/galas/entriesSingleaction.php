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
  $entryID = mysqli_real_escape_string(LINK, trim(htmlspecialchars($_POST['entryID'])));
}
if (!empty($_POST['TimesRequired'])) {
  $times = mysqli_real_escape_string(LINK, trim(htmlspecialchars($_POST['TimesRequired'])));
}

if (isset($entryID)) {

    if ($times != 1) {
    for ($i=0; $i<sizeof($swimsArray); $i++) {
      if (!empty($_POST[$swimsArray[$i]])) {
        $entriesArray[$i] = mysqli_real_escape_string(LINK, trim(htmlspecialchars($_POST[$swimsArray[$i]])));
        $counter++;
      }
      else {
          $entriesArray[$i] = 0;
      }
    }
    $set = "";
    for ($i=0; $i<sizeof($swimsArray); $i++) {
      if ($i < (sizeof($swimsArray)-1)) {
        $set .= "" . $swimsArray[$i] . " = '" . $entriesArray[$i] . "', ";
      }
      else {
        $set .= "" . $swimsArray[$i] . " = '" . $entriesArray[$i] . "' ";
      }
    }

    $sql = "UPDATE `galaEntries` SET $set WHERE EntryID = '$entryID';";
    $action = mysqli_query(LINK, $sql);
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
  else {
    for ($i=0; $i<sizeof($swimsArray); $i++) {
      if ((!empty($_POST[$swimsTimeArray[$i] . "Mins"])) || (!empty($_POST[$swimsTimeArray[$i] . "Secs"])) || (!empty($_POST[$swimsTimeArray[$i] . "Hunds"]))) {
        $mins = mysqli_real_escape_string(LINK, sprintf('%02d',trim(htmlspecialchars($_POST[$swimsTimeArray[$i] . "Mins"]))));
        $secs = mysqli_real_escape_string(LINK, sprintf('%02d',trim(htmlspecialchars($_POST[$swimsTimeArray[$i] . "Secs"]))));
        $hunds = mysqli_real_escape_string(LINK, sprintf('%02d',trim(htmlspecialchars($_POST[$swimsTimeArray[$i] . "Hunds"]))));
        if (($mins>0) || ($secs>0) || ($hunds>0)) {
          $entriesTimeArray[$i] = $mins . ":" . $secs . "." . $hunds;
          $entriesArray[$i] = 1;
          $counter++;
        }
        else {
          $entriesTimeArray[$i] = null;
          $entriesArray[$i] = 0;
        }
      }
      else {
          $entriesTimeArray[$i] = null;
          $entriesArray[$i] = 0;
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
    $action = mysqli_query(LINK, $sql);
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

  $sql = "SELECT members.MForename, members.MSurname, galaEntries.EntryID, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.EntryID = '$entryID';";
  $result = mysqli_query(LINK, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $pagetitle = $title = "Enter a Gala";
  $content .= "<p class=\"lead\">You have successfully updated " . $row['MForename'] . " " . $row['MSurname'] . "'s entry into " . $row['GalaName'] . ".</p>";
  $content .= "<ul>" . $entryList . "</ul>";
  $content .= "<p><a class=\"btn btn-outline-dark\" href=\"" . autoUrl("galas/entries/" . $row['EntryID'] . "") . "\">Return to entry</a></p>";
  $to = $row['Forename'] . " " . $row['Surname'] . "<" . $row['EmailAddress'] . ">";
  $subject = "Your Updated Gala Entry";
  $message = "<h1>Hello " . $row['Forename'] . " " . $row['Surname'] . "</h1>";
  $message .= "<p>Here's the details of your updated Gala Entry for " . $row['MForename'] . " " . $row['MSurname'] . " to the " . $row['GalaName'] . ".</p>";
  $message .= "<ul>" . $entryList . "</ul>";
  if ($row['GalaFeeConstant'] == 1) {
    $content .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . number_format(($counter*$row['GalaFee']),2,'.','') . "</strong></p>";
    $message .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . number_format(($counter*$row['GalaFee']),2,'.','') . "</strong></p>";
  }
  notifySend($to, $subject, $message);
}
else {
  $pagetitle = $title = "An error occurred";
  $content = "<div class=\"alert alert-warning\"><strong>An error occurred</strong> <br>We could not add your entry.</div>";
}
?>
