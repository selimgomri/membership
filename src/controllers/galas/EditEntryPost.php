<?php

global $db;

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
$entriesArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

$sql = null;

if ($_SESSION['AccessLevel'] == "Parent") {
  $sql = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ? AND members.UserID = ? ORDER BY `galas`.`GalaDate` DESC;");
  $sql->execute([$id, $_SESSION['UserID']]);
} else {
  $sql = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ? ORDER BY `galas`.`GalaDate` DESC;");
  $sql->execute([$id]);
}
$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$closingDate = new DateTime($row['ClosingDate'], new DateTimeZone('Europe/London'));
$theDate = new DateTime('now', new DateTimeZone('Europe/London'));

if (bool($row['Charged']) || bool($row['EntryProcessed']) || ($closingDate < $theDate && ($_SESSION['AccessLevel'] != 'Admin' && $_SESSION['AccessLevel'] != 'Galas')) || bool($row['Locked'])) {
  halt(404);
}

$numEntered = 0;
for ($i = 0; $i < sizeof($entriesArray); $i++) {
  if ($_POST[$swimsArray[$i]]) {
    $entriesArray[$i] = true;
    $numEntered++;
  }
}

$galaFee = 0;
if ($row['GalaFeeConstant']) {
  $galaFee = number_format($row['GalaFee'] * $numEntered, 2);
} else {
  $galaFee = number_format($_POST['galaFee'], 2);
}

try {
  $update = $db->prepare("UPDATE galaEntries SET 50Free = ?, 100Free = ?, 200Free = ?, 400Free = ?, 800Free = ?, 1500Free = ?, 50Breast = ?, 100Breast = ?, 200Breast = ?, 50Fly = ?, 100Fly = ?, 200Fly = ?, 50Back = ?, 100Back = ?, 200Back = ?, 100IM = ?, 150IM = ?, 200IM = ?, 400IM = ?, FeeToPay = ? WHERE EntryID = ?");
  $updateArray = array_merge($entriesArray, [$galaFee, $id]);
  $update->execute($updateArray);

  $sql = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ? ORDER BY `galas`.`GalaDate` DESC;");
  $sql->execute([$id]);
  $row = $sql->fetch(PDO::FETCH_ASSOC);

  $subject = "Your Updated " . $row['GalaName'] . " Entry";
  $message = "";
  if ($_SESSION['AccessLevel'] != 'Parent') {
    $message .= "<p><strong>Changes have been made to this gala entry by a member if staff. This is a courtesy email for you.</strong></p>";
  }
  $message .= "<p>Here are the details of your updated Gala Entry for " . htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . " to " . htmlspecialchars($row['GalaName']) . ".</p>";
  $message .= "<ul>" . $entryList . "</ul>";
  if ($row['GalaFeeConstant']) {
    $message .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . $galaFee . "</strong></p>";
  } else {
    $message .= "<p>The <strong>total fee payable is &pound;" . $galaFee . "</strong>. If you have entered this amount incorrectly, you may incur extra charges from the club or gala host.</p>";
  }
  $message .= '<p>You have entered ' . htmlspecialchars($row['MForename']) . ' into the following events;</p><ul>';
  for ($i = 0; $i < sizeof($entriesArray); $i++) {
    if ($entriesArray[$i]) {
      $message .= '<li>' . $swimsTextArray[$i] . '</li>';
    }
  }
  $message .= '</ul>';
  $message .= '<p>If you have any questions, please contact the ' . htmlspecialchars(env('CLUB_NAME')) . ' gala team as soon as possible.</p>';
  $notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
  `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 1, 'Galas')";
  $db->prepare($notify)->execute([$row['UserID'], $subject, $message]);
  $_SESSION['UpdateSuccess'] = true;
} catch (Exception $e) {
  $_SESSION['UpdateError'] = true;
}

header("Location: " . autoUrl("galas/entries/" . $id));