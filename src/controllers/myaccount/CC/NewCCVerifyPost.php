<?php

global $db;

try {
  if ($_SESSION['AddNotifyCC']['AuthCode'] = $_POST['auth']) {
    $insert = $db->prepare("INSERT INTO notifyAdditionalEmails (UserID, Name, EmailAddress) VALUES (?, ?, ?)");
    $insert->execute([$_SESSION['UserID'], $_SESSION['AddNotifyCC']['Name'], $_SESSION['AddNotifyCC']['EmailAddress']]);
    $_SESSION['AddNotifySuccess'] = true;
  } else {
    // Invalid Code
    $_SESSION['AddNotifyError'] = true;
  }
} catch (Exception $e) {
  // Error
  $_SESSION['AddNotifyError'] = true;
}

unset($_SESSION['AddNotifyCC']);

header("Location: " . autoUrl("myaccount/email"));
