<?php

try {

  if ($user == null) {
    halt(400);
  }

  if (!SCDS\CSRF::verify()) {
    halt(403);
  }

  $db = app()->db;
  $tenant = app()->tenant;

  $query = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE
  UserID = ? AND Tenant = ?");
  $query->execute([
    $user,
    $tenant->getId()
  ]);
  $userInfo = $query->fetch(PDO::FETCH_ASSOC);
  $query->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
    $tenant->getId()
  ]);
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
  $userEmail = $userInfo['EmailAddress'];
  $myName = $curUserInfo['Forename'] . ' ' . $curUserInfo['Surname'];

  $from = "noreply@" . getenv('EMAIL_DOMAIN');
  $fromName = app()->tenant->getKey('CLUB_NAME');
  if ($_POST['from'] == "current-user") {
    $fromName = $myName;
  }

  $replyAddress = getUserOption($_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], 'NotifyReplyAddress');
  $replyName = $myName;

  if (!($replyAddress && isset($_POST['ReplyToMe']) && bool($_POST['ReplyToMe']))) {
    $replyAddress = app()->tenant->getKey('CLUB_EMAIL');
    $replyName = app()->tenant->getKey('CLUB_NAME');
  }

  $cc = $bcc = null;

  $subject = $_POST['subject'];

  $messagePlain = \Soundasleep\Html2Text::convert($message);

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
          $_SESSION['TENANT-' . app()->tenant->getId()]['TooLargeError'] = true;
        } else {
          $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError'] = true;
        }
        throw new Exception();
      } else if (false) {
        // Probably not a text file
        reportError($_FILES['file-upload']['type'][$i]);
        $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError'] = true;
        throw new Exception();
      } else if ($_FILES['file-upload']['size'][$i] > 3145728) {
        // Too large, stop
        // reportError($_FILES['file-upload']['size'][$i]);
        $_SESSION['TENANT-' . app()->tenant->getId()]['TooLargeError'] = true;
        throw new Exception();
      } else if ($_FILES['file-upload']['size'][$i] > 0) {
        // Store uploaded files in filestore, if exists
        $collectiveSize += $_FILES['file-upload']['size'][$i];
        $attachments[] = [
          'encoded' => base64_encode(file_get_contents($_FILES['file-upload']['tmp_name'][$i])),
          'mime' => mime_content_type($_FILES['file-upload']['tmp_name'][$i]),
          'filename' => $_FILES['file-upload']['name'][$i],
          'disposition' => 'attachment',
          'tmp_name' => $_FILES['file-upload']['tmp_name'][$i],
        ];
      } else {
        // File upload error (no size)
        reportError($_FILES);
        $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError'] = true;
        throw new Exception();
      }
    }
  }

  if ($collectiveSize > 10485760) {
    // Collectively too large attachments
    $_SESSION['TENANT-' . app()->tenant->getId()]['CollectiveSizeTooLargeError'] = true;
    throw new Exception();
  }

  $mailObject = new \CLSASC\SuperMailer\CreateMail();
  $mailObject->setHtmlContent($messagePlain);
  $plain_text = $mailObject->getFormattedPlain();
  //$plain_text = str_replace(';', '', $plain_text);
  $plainTextContent = new \SendGrid\Mail\PlainTextContent($plain_text);;

  $email = new \SendGrid\Mail\Mail();
  $email->setFrom($from, $fromName);
  $email->setSubject($subject);
  $email->addTo($userEmail, $name);
  $email->addContent("text/plain", $plain_text);
  $email->setReplyTo($replyAddress, $replyName);

  // reportError([
  //   $_POST,
  //   $_FILES,
  //   $attachments
  // ]);

  foreach ($attachments as $attachment) {
    $email->addAttachment(
      $attachment['encoded'],
      $attachment['mime'],
      $attachment['filename'],
      $attachment['disposition']
    );
  }

  // Get coaches
  if (isset($swimmer) && isset($_POST['coach-send']) && bool($_POST['coach-send'])) {
    $bccEmails = [];

    // Get member squad(s)
    $getSquad = $db->prepare("SELECT Squad FROM squadMembers WHERE Member = ?");
    $getSquad->execute([
      $swimmer,
    ]);

    while ($squad = $getSquad->fetchColumn()) {
      // Get coaches for squads
      $getCoaches = $db->prepare("SELECT Forename, Surname, EmailAddress FROM coaches INNER JOIN users ON users.UserID = coaches.User WHERE coaches.Squad = ?");
      $getCoaches->execute([
        $squad
      ]);

      while ($coach = $getCoaches->fetch(PDO::FETCH_ASSOC)) {
        $bccEmails[$coach['EmailAddress']] = $coach['Forename'] . ' ' . $coach['Surname'];
      }
    }
    
    $email->addBccs($bccEmails);
  }

  $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
  $response = $sendgrid->send($email);

  if ($response->statusCode() == "202") {
    $_SESSION['TENANT-' . app()->tenant->getId()]['NotifyIndivSuccess'] = true;
  } else {
    throw new Exception('Invalid request to SendGrid');
  }
} catch (Exception $e) {
  $_SESSION['TENANT-' . app()->tenant->getId()]['NotifyIndivSuccess'] = false;
  $_SESSION['TENANT-' . app()->tenant->getId()]['NotifyIndivPostContent'] = $_POST;
  reportError($e);
} finally {

  if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NotifyIndivSuccess']) && !$_SESSION['TENANT-' . app()->tenant->getId()]['NotifyIndivSuccess']) {
    // Return to composer
    if (isset($returnToSwimmer) && $returnToSwimmer) {
      header("location: " . autoUrl("members/" . $id . "/contact-parent"));
    } else if (isset($userOnly) && $userOnly) {
      header("location: " . autoUrl("users/" . $user . "/email"));
    } else {
      header("location: " . autoUrl(""));
    }
  } else {
    if (isset($returnToSwimmer) && $returnToSwimmer) {
      header("location: " . autoUrl("members/" . $id));
    } else if (isset($userOnly) && $userOnly) {
      header("location: " . autoUrl("users/" . $user));
    } else {
      header("location: " . autoUrl("notify"));
    }
  }
}
