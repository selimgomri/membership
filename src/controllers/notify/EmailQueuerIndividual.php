<?php

if (is_null($user)) {
  halt(400);
}

if (!SCDS\FormIdempotency::verify() || !SCDS\CSRF::verify()) {
  halt(403);
}

global $db;
$query = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE
UserID = ?");
$query->execute([$user]);
$userInfo = $query->fetch(PDO::FETCH_ASSOC);
$query->execute([$_SESSION['UserID']]);
$curUserInfo = $query->fetch(PDO::FETCH_ASSOC);

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

$name = $userInfo['Forename'] . ' ' . $userInfo['Surname'];
$email = $userInfo['EmailAddress'];
$myName = $curUserInfo['Forename'] . ' ' . $curUserInfo['Surname'];
$myNakedEmail = $curUserInfo['EmailAddress'];
$myEmail; $canReply; $reply;

$cc = $bcc = null;

$subject = $_POST['subject'];

$messagePlain = \Soundasleep\Html2Text::convert($message);

if (notifySend("", $subject, $messagePlain, $name, $email, ["Email" => $myEmail, "Name" => $myName, "Reply-To" => $reply, "CC" => $cc, "BCC" => $bcc, 'PlainText' => true])) {
  $_SESSION['NotifyIndivSuccess'] = true;
} else {
  $_SESSION['NotifyIndivSuccess'] = false;
}

if ($returnToSwimmer) {
  header("Location: " . autoUrl("swimmers/" . $id));
} else {
  header("Location: " . autoUrl("notify"));
}
