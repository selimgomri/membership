<?php

if (is_null($user)) {
  halt(400);
}

if (!SCDS\FormIdempotency::verify()) {
  halt(403);
}

global $db;
$query = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE
UserID = ?");
$query->execute([$user]);
$userInfo = $query->fetchAll(PDO::FETCH_ASSOC);
$query->execute([$_SESSION['UserID']]);
$curUserInfo = $query->fetchAll(PDO::FETCH_ASSOC);

if (sizeof($userInfo) != 1) {
  halt(400);
}

$to_remove = [
  "<p>&nbsp;</p>",
  "<p></p>",
  "<p> </p>",
  "\r",
  "\n",
  '<div dir="auto">&nbsp;</div>',
  '&nbsp;'
];

$message = $message = str_replace($to_remove, "", $_POST['message']);

$userInfo = $userInfo[0];
$curUserInfo = $curUserInfo[0];

$name = $userInfo['Forename'] . ' ' . $userInfo['Surname'];
$email = $userInfo['EmailAddress'];
$myName = $curUserInfo['Forename'] . ' ' . $curUserInfo['Surname'];
$myNakedEmail = $curUserInfo['EmailAddress'];
$myEmail; $canReply; $reply;

$cc = $bcc = null;

if (strpos($curUserInfo['EmailAddress'], '@chesterlestreetasc.co.uk') == mb_strlen(str_replace('@chesterlestreetasc.co.uk', '', $curUserInfo['EmailAddress']))) {
  $myEmail = $curUserInfo['EmailAddress'];
  $reply = $myEmail;
  $bcc = [
    $myEmail => $myName
  ];
} else {
  $myEmail = mb_strtolower($curUserInfo['Forename'] . '.' . $curUserInfo['Surname'] . ".volunteer-noreply@" . EMAIL_DOMAIN);
  $canReply = "As you don't have a club email address, we can't allow parents to directly reply to you. If parents reply, their email will go to our enquiries team, who can forward it on to you.";
  $reply = "enquiries+replyto-" . mb_strtolower($curUserInfo['Forename'] . '-' . $curUserInfo['Surname']) . "@" . EMAIL_DOMAIN;
  $message .= '<p class="small text-muted">This message was sent by ' . $myName . ', a volunteer at our club who does not have a club email address. In order to comply with the General Data Protection Regulation, we cannot provide you with the details to reply directly to their personal email address. If you reply to this email, it will go to our Enquiries Team who can pass your message on to ' . $myName . '.</p>';

  $bcc = [
    $myNakedEmail => $myName
  ];
}

if ($_POST['from'] == 'club-sending-account') {
  $myEmail = "noreply@" . EMAIL_DOMAIN;
  $myName = CLUB_NAME;
}

$subject = $_POST['subject'];

if (notifySend("", $subject, $message, $name, $email, ["Email" => $myEmail, "Name" => $myName, "Reply-To" => $reply, "CC" => $cc, "BCC" => $bcc, 'PlainText' => true])) {
  $_SESSION['NotifyIndivSuccess'] = true;
} else {
  $_SESSION['NotifyIndivSuccess'] = false;
}
if ($returnToSwimmer) {
  header("Location: " . autoUrl("swimmers/" . $id));
} else {
  header("Location: " . autoUrl("notify"));
}
