<?php
$content = "";
$galaID = $galaName = $courseLength = $galaVenue = $closingDate = $galaDate = $galaFeeConstant = $galaFee = $hyTek = "";

if (!empty($_POST['galaID'])) {
  $galaID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaID'])));
}
if (!empty($_POST['galaname'])) {
  $galaName = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaname'])));
}
if (!empty($_POST['length'])) {
  $courseLength = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['length'])));
}
if (!empty($_POST['venue'])) {
  $galaVenue = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['venue'])));
}
if (!empty($_POST['closingDate'])) {
  $closingDate = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['closingDate'])));
}
if (!empty($_POST['galaDate'])) {
  $galaDate = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaDate'])));
}
if (!empty($_POST['GalaFeeConstant'])) {
  $galaFeeConstant = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['GalaFeeConstant'])));
}
if (!empty($_POST['GalaFee'])) {
  $galaFee = mysqli_real_escape_string($link, number_format(trim(htmlspecialchars($_POST['GalaFee'])),2,'.',''));
}
if (!empty($_POST['HyTek'])) {
  $hyTek = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['HyTek'])));
}
if ($galaFeeConstant != 1) {
  $galaFeeConstant = 0;
  $galaFee = 0.00;
}

if (isset($galaID  )) {



  $sql = "UPDATE `galas` SET  GalaName = '$galaName', CourseLength = '$courseLength', GalaVenue = '$galaVenues', ClosingDate = '$closingDate', GalaDate = '$galaDate', GalaFeeConstant = '$galaFeeConstant', GalaFee = '$galaFee', HyTek = '$hyTek' WHERE GalaID = '$galaID' ;";
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

  $sql = "SELECT members.MForename, members.MSurname, galaEntries.EntryID, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.EntryID = '$entryID';";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $pagetitle = $title = "Enter a Gala";
  $content .= "<p class=\"lead\">You have successfully updated " . $row['MForename'] . " " . $row['MSurname'] . "'s entry into " . $row['GalaName'] . ".</p>";
  $content .= "<ul>" . $entryList . "</ul>";
  $content .= "<p><a class=\"btn btn-success\" href=\"" . autoUrl("galas/entries/" . $row['EntryID'] . "") . "\">Return to entry</a></p>";
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
