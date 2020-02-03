<?php

$_SESSION['NotifyPostData'] = $_POST;

global $db;

$db->beginTransaction();

try {
  if (sizeof($_POST) == 0) {
    $_SESSION['TooLargeError'] = true;
    throw new Exception();
  }

  if (!SCDS\FormIdempotency::verify()) {
    $_SESSION['FormError'] = true;
    throw new Exception();
  }

  if (!SCDS\CSRF::verify()) {
    $_SESSION['FormError'] = true;
    throw new Exception();
  }

  $replyAddress = getUserOption($_SESSION['UserID'], 'NotifyReplyAddress');

  $to_remove = [
    "<p>&nbsp;</p>",
    "<p></p>",
    "<p> </p>",
    "\r",
    "\n",
    '<div dir="auto">&nbsp;</div>',
    '&nbsp;'
  ];

  // Handle attachments early doors
  $attachments = [];
  $collectiveSize = 0;
  for ($i = 0; $i < sizeof($_FILES['file-upload']['tmp_name']); $i++) {
    if (is_uploaded_file($_FILES['file-upload']['tmp_name'][$i])) {

      if (bool($_FILES['file-upload']['error'][$i])) {
        // Error
        // reportError($_FILES['file-upload']['error'][$i]);
        if ($_FILES['file-upload']['error'][$i] == 2) {
          // Too large
          $_SESSION['TooLargeError'] = true;
        } else {
          $_SESSION['UploadError'] = true;
        }
        throw new Exception();
      } else if (false/*$_FILES['file-upload']['type'][$i] != 'text/plain' && $_FILES['file-upload']['type'][$i] != 'application/octet-stream'*/) {
        // Probably not a text file
        reportError($_FILES['file-upload']['type'][$i]);
        $_SESSION['UploadError'] = true;
        throw new Exception();
      } else if ($_FILES['file-upload']['size'][$i] > 3000000) {
        // Too large, stop
        // reportError($_FILES['file-upload']['size'][$i]);
        $_SESSION['TooLargeError'] = true;
        throw new Exception();
      } else {
        // Store uploaded files in filestore, if exists
        if (env('FILE_STORE_PATH')) {
          // Work out filename for upload
          $date = new DateTime('now', new DateTimeZone('Europe/London'));
          $urlPath = 'notify/attachments/' . $date->format("Y/m/d") . '/';
          $path = env('FILE_STORE_PATH') . $urlPath;
          $hash = hash('sha256', $_FILES['file-upload']['tmp_name'][$i]);
          $filenamePath = $path . $hash;
          $url = $urlPath . $hash;
          $count = 0; $countText = "";
          while (file_exists($path . $countText . $hash)) {
            $count++;
            $countText = (string) $count;
          }
          if ($count > 0) {
            $filenamePath = $path . $countText . $hash;
            $url = $urlPath . $countText . $hash;
          }
        }

        $collectiveSize += $_FILES['file-upload']['size'][$i];
        $attachments[] = [
          'encoded' => base64_encode(file_get_contents($_FILES['file-upload']['tmp_name'][$i])),
          'mime' => mime_content_type($_FILES['file-upload']['tmp_name'][$i]),
          'filename' => $_FILES['file-upload']['name'][$i],
          'disposition' => 'attachment',
          'tmp_name' => $_FILES['file-upload']['tmp_name'][$i],
          'store_name' => $filenamePath,
          'url' => $url,
          'uploaded' => false,
        ];
      }
    }
  }

  if ($collectiveSize > 10000000) {
    // Collectively too large attachments
    $_SESSION['CollectiveSizeTooLargeError'] = true;
    throw new Exception();
  }

  if (env('FILE_STORE_PATH')) {
    for ($i = 0; $i < sizeof($attachments); $i++) {
      if (!is_writeable($attachments[$i]['store_name'])) {
        // Try making folders
        $dir = explode('/', $attachments[$i]['store_name']);
        $path = "";
        $tried = [];
        for ($i = 0; $i < sizeof($dir)-1; $i++) {
          $path .= $dir[$i];
          if (!is_dir($path)) {
            mkdir($path);
            $tried[] = $path;
          }
          $path .= '/';
        }
        if (!is_writeable($path)) {
          reportError([$tried, $path, $attachments[$i]['store_name']]);
        }
      }
      if (move_uploaded_file($attachments[$i]['tmp_name'], $attachments[$i]['store_name'])) {
        $attachments[$i]['uploaded'] = true;
      } else {
        reportError($_FILES['file-upload']);
      }
    }
  }

  // reportError($attachments);

  $subject = $_POST['subject'];
  $message = str_replace($to_remove, "", $_POST['message']);
  if ($_SESSION['AccessLevel'] != "Admin" && !($replyAddress && isset($_POST['ReplyToMe']) && bool($_POST['ReplyToMe']))) {
    $name = getUserName($_SESSION['UserID']);
    $message .= '<p class="small text-muted">Sent by ' . $name . '. Reply to this email to contact our Enquiries Team who can pass your message on to ' . $name . '.</p>';
  }
  $force = 0;
  $sender = $_SESSION['UserID'];
  if (isset($_POST['force']) && ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Galas")) {
    $force = 1;
  }

  $squads = null;
  if ($_SESSION['AccessLevel'] != 'Parent') {
    $squads = $db->query("SELECT `SquadName`, `SquadID` FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC;");
  } else {
    $squads = $db->prepare("SELECT `SquadName`, `SquadID` FROM `squads` INNER JOIN squadReps ON squadReps.Squad = squads.SquadID WHERE squadReps.User = ? ORDER BY `SquadFee` DESC, `SquadName` ASC;");
    $squads->execute([$_SESSION['UserID']]);
  }
  $row = $squads->fetchAll(PDO::FETCH_ASSOC);

  $lists = null;
  if ($_SESSION['AccessLevel'] != 'Parent') {
    $lists = $db->query("SELECT targetedLists.ID, targetedLists.Name FROM `targetedLists` ORDER BY `Name` ASC;");
  } else {
    $lists = $db->prepare("SELECT targetedLists.ID, targetedLists.Name FROM `targetedLists` INNER JOIN listSenders ON listSenders.List = targetedLists.ID WHERE listSenders.User = ? ORDER BY `Name` ASC;");
    $lists->execute([$_SESSION['UserID']]);
  }
  $lists = $lists->fetchAll(PDO::FETCH_ASSOC);

  $galas = $db->prepare("SELECT GalaName, GalaID FROM `galas` WHERE GalaDate >= ? ORDER BY `GalaName` ASC;");
  $date = new DateTime('-1 week', new DateTimeZone('Europe/London'));
  $galas->execute([$date->format('Y-m-d')]);

  $query = $squadsQuery = $listsQuery = $galaQuery = "";

  $squads = $listsArray = $galasArray = [];

  for ($i = 0; $i < sizeof($row); $i++) {
    if ($squadsQuery != "" && $_POST[$row[$i]['SquadID']] == 1) {
      $squadsQuery .= "OR";
    }
    if ($_POST[$row[$i]['SquadID']] == 1) {
      $squadsQuery .= " `SquadID` = '" . $row[$i]['SquadID'] . "' ";
      $squads[$row[$i]['SquadID']] = $row[$i]['SquadName'];
    }
  }

  for ($i = 0; $i < sizeof($lists); $i++) {
    if ($listsQuery != "" && $_POST["TL-" . $lists[$i]['ID']] == 1) {
      $listsQuery .= "OR";
    }
    if ($_POST["TL-" . $lists[$i]['ID']] == 1) {
      $id = "TL-" . $lists[$i]['ID'];
      $id = substr_replace($id, '', 0, 3);
      $listsQuery .= " `ListID` = '" . $lists[$i]['ID'] . "' ";
      $listsArray[$lists[$i]['ID']] = $lists[$i]['Name'];
    }
  }

  while ($gala = $galas->fetch(PDO::FETCH_ASSOC)) {
    if ($galaQuery != "" && $_POST["GALA-" . $gala['GalaID']]) {
      $galaQuery .= "OR";
    }
    if ($_POST["GALA-" . $gala['GalaID']]) {
      $id = "TL-" . $lists[$i]['ID'];
      $id = substr_replace($id, '', 0, 3);
      $galaQuery .= " `GalaID` = '" . $gala['GalaID'] . "' ";
      $galasArray[$gala['GalaID']] = $gala['GalaName'];
    }
  }

  $toSendTo = [];
  $squadUsers = $listUsers = $galaUsers = null;

  if ($squadsQuery) {
    $squadUsers = $db->query("SELECT UserID FROM members WHERE (" . $squadsQuery . ") AND UserID IS NOT NULL");
    while ($u = $squadUsers->fetch(PDO::FETCH_ASSOC)) {
      $toSendTo[$u['UserID']] = $u['UserID'];
    }
  }
  if ($listsQuery) {
    $listUsers = $db->query("SELECT members.UserID FROM members INNER JOIN `targetedListMembers` ON targetedListMembers.ReferenceID = members.MemberID WHERE (" . $listsQuery . ") AND ReferenceType = 'Member' AND UserID IS NOT NULL");
    while ($u = $listUsers->fetch(PDO::FETCH_ASSOC)) {
      $toSendTo[$u['UserID']] = $u['UserID'];
    }

    $listUsers = $db->query("SELECT users.UserID FROM users INNER JOIN `targetedListMembers` ON targetedListMembers.ReferenceID = users.UserID WHERE (" . $listsQuery . ") AND ReferenceType = 'User'");
    while ($u = $listUsers->fetch(PDO::FETCH_ASSOC)) {
      $toSendTo[$u['UserID']] = $u['UserID'];
    }
  }
  if ($galaQuery && $_SESSION['AccessLevel'] != 'Parent') {
    $galaUsers = $db->query("SELECT users.UserID FROM ((`users` INNER JOIN `members` ON members.UserID = users.UserID) INNER JOIN `galaEntries` ON galaEntries.MemberID = members.MemberID) WHERE " . $galaQuery);
    while ($u = $galaUsers->fetch(PDO::FETCH_ASSOC)) {
      $toSendTo[$u['UserID']] = $u['UserID'];
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
      "Targeted_Lists" => $listsArray,
      "Galas" => $galasArray
    ],
    "Message" => [
      "Subject" => $subject,
      "Body" => $message
    ],
    "Metadata" => [
      "ForceSend" => $force
    ],
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

    if (!bool(env('IS_CLS'))) {
      $fromEmail .= '.' . urlencode(strtolower(str_replace(' ', '', CLUB_CODE)));
    }

    $fromEmail .= '@' . env('EMAIL_DOMAIN');

    $recipientGroups["NamedSender"] = [
      "Email" => $fromEmail,
      "Name" => $userSending
    ];
  }

  if ($replyAddress && isset($_POST['ReplyToMe']) && bool($_POST['ReplyToMe'])) {
    $recipientGroups["ReplyToMe"] = [
      "Email" => $replyAddress,
      "Name" => $_SESSION['Forename'] . ' ' . $_SESSION['Surname'],
    ];
  }

  reportError($attachments);
  if (sizeof($attachments) > 0) {
    $recipientGroups["Attachments"] = [];
  }
  foreach($attachments as $attachment) {
    if ($attachment['uploaded']) {
      $recipientGroups["Attachments"][] = [
        'Filename' => $attachment['filename'],
        'URI' => $attachment['url'],
        'MIME' => $attachment['mime'],
      ];
    }
  }

  $json = json_encode($recipientGroups);
  $date = new DateTime('now', new DateTimeZone('UTC'));
  $dbDate = $date->format('Y-m-d H:i:s');

  $sql = "INSERT INTO `notifyHistory` (`Sender`, `Subject`, `Message`,
  `ForceSend`, `Date`, `JSONData`) VALUES (?, ?, ?, ?, ?, ?)";
  $pdo_query = $db->prepare($sql);
  $pdo_query->execute([$_SESSION['UserID'], $subject, $message, $force, $dbDate, $json]);

  $id = $db->lastInsertId();

  $count = sizeof($toSendTo);

  $insert = $db->prepare("INSERT INTO `notify` (`UserID`, `MessageID`, `Subject`, `Message`, `Status`, `Sender`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, 'Sent', ?, ?, 'Notify')");

  foreach ($toSendTo as $userid => $user) {
    try {
      $insert->execute([$userid, $id, null, null, $sender, $force]);
    } catch (PDOException $e) {
      reportError($e);
    }
  }

  if ($_SESSION['AccessLevel'] != "Admin" && $force == 1) {
    $sql = "SELECT `UserID` FROM `users` WHERE `AccessLevel` = 'Admin'";
    try {
      $pdo_query = $db->prepare($sql);
      $pdo_query->execute([$userid, $id, $subject, $message, $sender, $force]);
    } catch (PDOException $e) {
      reportError($e);
    }

    try {
      $sendToTeam = $db->prepare("INSERT INTO `notify` (`UserID`, `MessageID`, `Subject`, `Message`, `Status`, `Sender`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, 'Queued', ?, ?, 'Notify-Audit')");
    } catch (PDOException $e) {
      reportError($e);
    }

    $gdpr_question = '<p>You have force sent the below message. Please contact <a href="mailto:gdpr@chesterlestreetasc.co.uk">gdpr@chesterlestreetasc.co.uk</a> to explain the rationale for using <strong>Force Send</strong> for this email.</p><hr>' . $message . '<p class="small text-muted">Sent via Notify, our custom built email notification service.</p>';
    $sendToTeam->execute([$_SESSION['UserID'], null, "GDPR Compliance: " . $subject, $gdpr_question, $sender, $force]);

    $intro = '
    <p>We\'re sending you this email because you\'re an administrator at ' . env('CLUB_NAME') . '.</p>
    <p>' . getUserName($_SESSION['UserID']) . ' has force sent the following email, overriding parent subscription options. We send these updates about emails which have been force sent in order to ensure compliance with GDPR rules.</p>
    <p>Emails should only be force sent when they are of high importance. An example would be to inform parents of a session cancellation.</p>
    <hr>';
    $message_admin = $intro . $message . '<p class="small text-muted">Sent via Notify, our custom built email notification service.</p>';

    $row = $pdo_query->fetchAll(PDO::FETCH_ASSOC);
    for ($i = 0; $i < sizeof($row); $i++) {
      try {
        $sendToTeam->execute([$row[$i]['UserID'], null, "GDPR Alert: " . $subject, $message_admin, $sender, $force]);
      } catch (PDOException $e) {
        reportError($e);
      }
    }
  }

  $_SESSION['NotifySuccess'] = [
    "Count" => $count,
    "Force" => $force
  ];

  $emailPrefix = '';
  if (!bool(env('IS_CLS'))) {
    $emailPrefix = mb_strtolower(trim(env('ASA_CLUB_CODE'))) . '-';
  }

  $club_address = "";
  $club = json_decode(CLUB_JSON);
  for ($i = 0; $i < sizeof($club->ClubAddress); $i++) {
  $club_address .= $club->ClubAddress[$i] . "\r\n";
  }

  global $db;
  $getExtraEmails = $db->prepare("SELECT Name, EmailAddress, ID FROM notifyAdditionalEmails WHERE UserID = ? AND Verified = '1'");

  $getPendingGroupMail = $db->prepare("SELECT ID, notifyHistory.Subject, notifyHistory.Message, notifyHistory.ForceSend, notifyHistory.JSONData FROM notifyHistory WHERE ID = ?");
  $getPendingGroupMail->execute([$id]);

  $getUsersForEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, notify.UserID, EmailID FROM notify INNER JOIN users ON notify.UserID = users.UserID WHERE MessageID = ?");

  // Completed It PDO Object
  // $completed = $db->prepare("UPDATE `notify` SET `Status` = ? WHERE `EmailID` = ?");

  while ($currentMessage = $getPendingGroupMail->fetch(PDO::FETCH_ASSOC)) {
    $getUsersForEmail->execute([$currentMessage['ID']]);

    $jsonData = json_decode($currentMessage['JSONData']);

    $mailObject = new \CLSASC\SuperMailer\CreateMail();
    $mailObject->setHtmlContent($currentMessage['Message']);

    $mailObject->showName();
    if (!$currentMessage['ForceSend']) { 
      $mailObject->setUnsubscribable();
    }

    $from = new \SendGrid\Mail\From("notify@" . env('EMAIL_DOMAIN'), env('CLUB_NAME'));
    if ($currentMessage['ForceSend']) {
      $from = new \SendGrid\Mail\From("noreply@" . env('EMAIL_DOMAIN'), env('CLUB_NAME'));
    }
    if ($jsonData->NamedSender->Email != null && $jsonData->NamedSender->Name) {
      $from = new \SendGrid\Mail\From($emailPrefix . "noreply@" . env('EMAIL_DOMAIN'), $jsonData->NamedSender->Name);
    }
    $tos = [];
    while ($user = $getUsersForEmail->fetch(PDO::FETCH_ASSOC)) {
      if ($currentMessage['ForceSend'] || isSubscribed($user['UserID'], 'Notify')) {
        $tos[] = new \SendGrid\Mail\To(
          $user['EmailAddress'],
          $user['Forename'] . ' ' . $user['Surname'],
          [
            '-name-' => $user['Forename'] . ' ' . $user['Surname'],
            '-unsub_link-' => autoUrl("notify/unsubscribe/" . dechex($user['UserID']) .  "/" . urlencode($user['EmailAddress']) . "/Notify")
          ]
        );
        $getExtraEmails->execute([$user['UserID']]);
        while ($extraEmails = $getExtraEmails->fetch(PDO::FETCH_ASSOC)) {
          $tos[] = new \SendGrid\Mail\To(
            $extraEmails['EmailAddress'],
            $extraEmails['Name'],
            [
              '-name-' => $extraEmails['Name'],
              '-unsub_link-' => autoUrl("cc/" . dechex($extraEmails['ID']) .  "/" . hash('sha256', $extraEmails['ID']) . "/unsubscribe")
            ]
          );
          $ccEmails[$extraEmails['EmailAddress']] = $extraEmails['Name'];
        }
        // $completed->execute(['Sent', $user['EmailID']]);
      } else {
        // $completed->execute(['No_Sub', $user['EmailID']]);
      }
    }
    $subject = $currentMessage['Subject'];
    $globalSubstitutions = [];
    $plain_text = $mailObject->getFormattedPlain();
    //$plain_text = str_replace(';', '', $plain_text);
    $plainTextContent = new \SendGrid\Mail\PlainTextContent($plain_text);
    $htmlContent = new \SendGrid\Mail\HtmlContent($mailObject->getFormattedHtml());

    $email = new \SendGrid\Mail\Mail(
      $from,
      $tos,
      $subject,
      $plainTextContent,
      $htmlContent,
      $globalSubstitutions
    );

    foreach ($attachments as $attachment) {
      $email->addAttachment(
        $attachment['encoded'],
        $attachment['mime'],
        $attachment['filename'],
        $attachment['disposition']
      );
    }

    if ($jsonData->ReplyToMe->Email != null && $jsonData->ReplyToMe->Name != null) {
      try {
        $email->setReplyTo($jsonData->ReplyToMe->Email, $jsonData->ReplyToMe->Name);
      } catch (Exception $e) {
        $email->setReplyTo(env('CLUB_EMAIL'), env('CLUB_NAME') . ' Enquiries');
      }
    } else {
      $email->setReplyTo(env('CLUB_EMAIL'), env('CLUB_NAME') . ' Enquiries');
    }

    $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
    $response = $sendgrid->send($email);
  }
  $db->commit();

  if (isset($_SESSION['NotifyPostData'])) {
    unset($_SESSION['NotifyPostData']);
  }

  header("Location: " . autoUrl("notify"));
} catch (Exception $e) {
  reportError($e);
  $db->rollback();
  header("Location: " . autoUrl("notify/new"));
}