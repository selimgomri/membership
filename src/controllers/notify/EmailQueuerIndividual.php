<?php

try {

  if ($user == null) {
    halt(400);
  }

  if (!SCDS\CSRF::verify()) {
    halt(403);
  }

  global $db;
  $query = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE
  UserID = ?");
  $query->execute([$user]);
  $userInfo = $query->fetch(PDO::FETCH_ASSOC);
  $query->execute([$_SESSION['UserID']]);
  $curUserInfo = $query->fetch(PDO::FETCH_ASSOC);

  if ($userInfo == null) {
    halt(404);
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

  $from = "noreply@" . env('EMAIL_DOMAIN');
  $fromName = env('CLUB_NAME');
  if ($_POST['from'] == "current-user") {
    $fromName = $myName;
  }

  $replyAddress = getUserOption($_SESSION['UserID'], 'NotifyReplyAddress');

  if (!($replyAddress && isset($_POST['ReplyToMe']) && bool($_POST['ReplyToMe']))) {
    $replyAddress = env('CLUB_EMAIL');
  }

  $cc = $bcc = null;

  $subject = $_POST['subject'];

  $messagePlain = \Soundasleep\Html2Text::convert($message);

  if (notifySend("", $subject, $messagePlain, $name, $email, ["Email" => $from, "Name" => $fromName, "Reply-To" => $replyAddress, "CC" => $cc, "BCC" => $bcc, 'PlainTextOnly' => true])) {
    $_SESSION['NotifyIndivSuccess'] = true;
  } else {
    $_SESSION['NotifyIndivSuccess'] = false;
  }
} catch (Exception $e) {
  $_SESSION['NotifyIndivSuccess'] = false;
} finally {

  if (isset($returnToSwimmer) && $returnToSwimmer) {
    header("location: " . autoUrl("members/" . $id));
  } else if (isset($userOnly) && $userOnly) {
    header("location: " . autoUrl("users/" . $user));
  } else {
    header("location: " . autoUrl("notify"));
  }

}