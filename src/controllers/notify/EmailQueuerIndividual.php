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
  $userEmail = $userInfo['EmailAddress'];
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
      } else if (false) {
        // Probably not a text file
        reportError($_FILES['file-upload']['type'][$i]);
        $_SESSION['UploadError'] = true;
        throw new Exception();
      } else if ($_FILES['file-upload']['size'][$i] > 3145728) {
        // Too large, stop
        // reportError($_FILES['file-upload']['size'][$i]);
        $_SESSION['TooLargeError'] = true;
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
        $_SESSION['UploadError'] = true;
        throw new Exception();
      }
    }
  }

  if ($collectiveSize > 10485760) {
    // Collectively too large attachments
    $_SESSION['CollectiveSizeTooLargeError'] = true;
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
  $email->setReplyTo($replyAddress, $from);

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

  $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
  $response = $sendgrid->send($email);

  if ($response->statusCode() == "202") {
    $_SESSION['NotifyIndivSuccess'] = true;
  } else {
    throw new Exception('Invalid request to SendGrid');
  }

} catch (Exception $e) {
  $_SESSION['NotifyIndivSuccess'] = false;
  $_SESSION['NotifyIndivPostContent'] = $_POST;
  reportError($e);
} finally {

  if (isset($_SESSION['NotifyIndivSuccess']) && !$_SESSION['NotifyIndivSuccess']) {
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