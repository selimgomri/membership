<?php

global $db;

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

try {

// JS Should catch existing entries so fail if one exists
$getGalaEntries = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE GalaID = ? AND MemberID = ?");
$getGalaEntries->execute([
  $_POST['gala'],
  $_POST['swimmer']
]);
if ($getGalaEntries->fetchColumn() > 0) {
  halt(403);
}

foreach ($swimsArray as $swim) {
  if ($_POST[$swim]) {
    $entriesArray[] = $_POST[$swim];
    $counter++;
  }
  else {
      $entriesArray[] = 0;
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
    $values .= "?, ";
  }
  else {
    $values .= "? ";
  }
}

$getGalaInformation = $db->prepare("SELECT GalaFee, GalaFeeConstant, GalaName FROM galas WHERE GalaID = ?");
$getGalaInformation->execute([$_POST['gala']]);
$row = $getGalaInformation->fetch(PDO::FETCH_ASSOC);

if ($row['GalaFeeConstant']) {
  $fee = number_format(($counter*$row['GalaFee']),2,'.','');
} else {
  $fee = 0;
  if (isset($_POST['galaFee'])) {
    $fee = number_format(($_POST['galaFee']),2,'.','');
    //debitWallet($_SESSION['UserID'], $fee, "Gala Entry into " . $row['GalaName'] . " (Holding Fee)");
  }
}

$insert = $db->prepare("INSERT INTO `galaEntries` (EntryProcessed, Charged, `MemberID`, `GalaID`, " . $swims . ", `TimesRequired`, `FeeToPay`) VALUES (?, ?, ?, ?, " . $values . ", ?, ?)");

$array = array_merge([0, 0, $_POST['swimmer'], $_POST['gala']], $entriesArray);
$array = array_merge($array, [0, $fee]);

pre($array);

$insert->execute($array);

$entryList = "";
$get = $db->prepare("SELECT * FROM (galaEntries INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ?");
$get->execute([$_POST['swimmer'], $_POST['gala']]);
$row = $get->fetch(PDO::FETCH_ASSOC);
// Print <li>Swim Name</li> for each entry
for ($y=0; $y<sizeof($swimsArray); $y++) {
  if ($row[$swimsArray[$y]] == 1) {
    $entryList .= "<li>" . $swimsTextArray[$y] . "</li>";
  }
}

$get = $db->prepare("SELECT members.MForename, members.MSurname, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ?");
$get->execute([$_POST['swimmer'], $_POST['gala']]);
$row = $get->fetch(PDO::FETCH_ASSOC);
$to = $row['Forename'] . " " . $row['Surname'] . "<" . $row['EmailAddress'] . ">";

$subject = $row['MForename'] . "'s Gala Entry to " . $row['GalaName'];
$message .= "<p>Here's the details of your Gala Entry for " . htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . " to " . htmlspecialchars($row['GalaName']) . ".</p>";
$message .= "<ul>" . $entryList . "</ul>";
if ($row['GalaFeeConstant'] == 1) {
  $message .= "<p>The fee for each swim is &pound;" . number_format($row['GalaFee'],2,'.','') . ", the <strong>total fee payable is &pound;" . number_format(($counter*$row['GalaFee']),2,'.','') . "</strong></p>";
} else {
  $message .= "<p>The <strong>total fee payable is &pound;" . $fee . "</strong>. If you have entered this amount incorrectly, you may incur extra charges from the club or gala host.</p>";
}
$notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
`ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 1, 'Galas')";
try {
  global $db;
  $email = $db->prepare($notify);
  $email->execute([$_SESSION['UserID'], $subject, $message]);
} catch (PDOException $e) {
  pre($e);
  //halt(500);
}

$_SESSION['SuccessfulGalaEntry'] = [
  "Gala" => $_POST['gala'],
  "Swimmer" => $_POST['swimmer']
];

} catch (Exception $e) {
  halt(500);
}

header("Location: " . autoUrl("galas/entergala"));