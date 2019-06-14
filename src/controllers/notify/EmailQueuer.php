<?php

ignore_user_abort(true);
set_time_limit(0);

global $db;

$to_remove = [
  "<p>&nbsp;</p>",
  "<p></p>",
  "<p> </p>",
  "\r",
  "\n",
  '<div dir="auto">&nbsp;</div>',
  '&nbsp;'
];

$subject = $_POST['subject'];
$message = str_replace($to_remove, "", $_POST['message']);
if ($_SESSION['AccessLevel'] != "Admin") {
  $name = getUserName($_SESSION['UserID']);
  $message .= '<p class="small text-muted">Sent by ' . $name . '. Reply to this email to contact our Enquiries Team who can pass your message on to ' . $name . '.</p>';
}
$force = 0;
$sender = $_SESSION['UserID'];
if (isset($_POST['force']) && $_SESSION['AccessLevel'] != "Coach") {
  $force = 1;
}

$sql = "SELECT `SquadName`, `SquadID` FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC";
try {
	$pdo_query = $db->prepare($sql);
  $pdo_query->execute();
} catch (PDOException $e) {
	halt(500);
}
$row = $pdo_query->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM `targetedLists` ORDER BY `Name` ASC";
try {
	$pdo_query = $db->prepare($sql);
  $pdo_query->execute();
} catch (PDOException $e) {
	halt(500);
}

pre($db->query("SELECT CHARSET('')")->fetchColumn());
while ($lists = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
  pre($lists);
}

$query = "";

$squads = $listsArray = [];

for ($i = 0; $i < sizeof($row); $i++) {
	if ($query != "" && $_POST[$row[$i]['SquadID']] == 1) {
		$query .= "OR";
	}
	if ($_POST[$row[$i]['SquadID']] == 1) {
		$query .= " `SquadID` = '" . $row[$i]['SquadID'] . "' ";
    $squads[$row[$i]['SquadID']] = $row[$i]['SquadName'];
	}
}

for ($i = 0; $i < sizeof($lists); $i++) {
	if ($query != "" && $_POST["TL-" . $lists[$i]['ID']] == 1) {
		$query .= "OR";
	}
	if ($_POST["TL-" . $lists[$i]['ID']] == 1) {
		$id = "TL-" . $lists[$i]['ID'];
		$id = substr_replace($id, '', 0, 3);
		$query .= " `ListID` = '" . $lists[$i]['ID'] . "' ";
    $listsArray[$lists[$i]['ID']] = $lists[$i]['Name'];
	}
}

$userSending = getUserName($sender);

$recipientGroups = [
  "Sender" => [
    "ID" => $sender,
    "Name" => $userSending
  ],
  "To" => [
    "Squads" => $squads,
    "Targeted_Lists" => $listsArray
  ],
  "Message" => [
    "Subject" => $subject,
    "Body" => $message
  ],
  "Metadata" => [
    "ForceSend" => $force
  ]
];

if ($_POST['from'] == "current-user") {
  $senderNames = explode(' ', $userSending);
  $fromEmail = "";
  for ($i = 0; $i < sizeof($senderNames); $i++) {
    $fromEmail .= urlencode(strtolower($senderNames[$i]));
    if ($i < sizeof($senderNames) - 1) {
      $fromEmail .= '.';
    }
  }

  if (!(defined('IS_CLS') && IS_CLS)) {
    $fromEmail .= '.' . urlencode(strtolower(str_replace(' ', '', CLUB_CODE)));
  }

  $fromEmail .= '@' . EMAIL_DOMAIN;

  $recipientGroups["NamedSender"] = [
    "Email" => $fromEmail,
    "Name" => $userSending
  ];
}

$json = json_encode($recipientGroups);
$date = new DateTime('now', new DateTimeZone('UTC'));
$dbDate = $date->format('Y-m-d H:i:s');

$sql = "INSERT INTO `notifyHistory` (`Sender`, `Subject`, `Message`,
`ForceSend`, `Date`, `JSONData`) VALUES (?, ?, ?, ?, ?, ?)";
try {
	$pdo_query = $db->prepare($sql);
  $pdo_query->execute([$_SESSION['UserID'], $subject, $message, $force, $dbDate, $json]);
} catch (PDOException $e) {
	halt(500);
}
$id = $db->lastInsertId();

$sql = $db->query("SELECT users.UserID, users.UserID FROM ((`users` INNER JOIN `members` ON
members.UserID = users.UserID) LEFT JOIN `targetedListMembers` ON
targetedListMembers.ReferenceID = members.MemberID) WHERE " . $query);
$users = $sql->fetchAll(PDO::FETCH_KEY_PAIR);
$count = 0;

foreach ($users as $userid => $user) {
	$sql = "INSERT INTO `notify` (`UserID`, `MessageID`, `Subject`, `Message`,
	`Status`, `Sender`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, 'Queued', ?, ?,
	'Notify')";
  try {
  	$pdo_query = $db->prepare($sql);
    $pdo_query->execute([$userid, $id, null, null, $sender, $force]);
  } catch (PDOException $e) {
  	halt(500);
  }
  $count++;
}

if ($_SESSION['AccessLevel'] != "Admin" && $force == 1) {
  $sql = "SELECT `UserID` FROM `users` WHERE `AccessLevel` = 'Admin'";
  try {
  	$pdo_query = $db->prepare($sql);
    $pdo_query->execute([$userid, $id, $subject, $message, $sender, $force]);
  } catch (PDOException $e) {
  	halt(500);
  }

  $sql = "INSERT INTO `notify` (`UserID`, `MessageID`, `Subject`, `Message`,
  `Status`, `Sender`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, 'Queued', ?, ?,
  'Notify-Audit')";
  try {
    $sendToTeam = $db->prepare($sql);
  } catch (PDOException $e) {
    halt(500);
  }

  $gdpr_question = '<p>You have force sent the below message. Please contact <a href="mailto:gdpr@chesterlestreetasc.co.uk">gdpr@chesterlestreetasc.co.uk</a> to explain the rationale for using <strong>Force Send</strong> for this email.</p><hr>' . $message . '<p class="small text-muted">Sent via Notify, our custom built email notification service.</p>';
  $sendToTeam->execute([$_SESSION['UserID'], null, "GDPR Compliance: " . $subject, $gdpr_question, $sender, $force]);

  $intro = '
  <p>We\'re sending you this email because you\'re an administrator at ' . CLUB_NAME . '.</p>
  <p>' . getUserName($_SESSION['UserID']) . ' has force sent the following email, overriding parent subscription options. We send these updates about emails which have been force sent in order to ensure compliance with GDPR rules.</p>
  <p>Emails should only be force sent when they are of high importance. An example would be to inform parents of a session cancellation.</p>
  <hr>';
  $message_admin = $intro . $message . '<p class="small text-muted">Sent via Notify, our custom built email notification service.</p>';

  $row = $pdo_query->fetchAll(PDO::FETCH_ASSOC);
  for ($i = 0; $i < sizeof($row); $i++) {
    try {
      $sendToTeam->execute([$row[$i]['UserID'], null, "GDPR Alert: " . $subject, $message_admin, $sender, $force]);
    } catch (PDOException $e) {
    	halt(500);
    }
  }
}

$_SESSION['NotifySuccess'] = [
  "Count" => $count,
  "Force" => $force
];

header("Location: " . autoUrl("notify"));