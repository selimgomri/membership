<?php

$db = app()->db;

$getAccount = $db->prepare("SELECT Forename, Surname, EmailAddress, `Password`, UserID id FROM users WHERE EmailAddress = ?");
$getAccount->execute([$_POST['email-addr']]);

$user = $getAccount->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

if (password_verify($_POST['password'], $user['Password'])) {
  // Check the accounts are not already linked
  $getLinked = $db->prepare("SELECT COUNT(*) FROM linkedAccounts WHERE Active = :active AND ((User = :cu AND LinkedUser = :lu) OR (User = :lu AND LinkedUser = :cu))");
  $getLinked->execute([
    'cu' => $_SESSION['UserID'],
    'lu' => $user['id'],
    'active' => 1
  ]);
  if ($getLinked->fetchColumn() > 0) {
    // Already linked
    $_SESSION['LinkedUserAlreadyExists'] = true;
  } else {
    // Add to DB
    $key = hash('sha256', 'LinkedUserHash' . random_int(0, 999999));
    $insert = $db->prepare("INSERT INTO linkedAccounts (User, LinkedUser, `Key`, Active) VALUES (?, ?, ?, ?)");
    $insert->execute([$_SESSION['UserID'], $user['id'], $key, 0]);
    $id = $db->lastInsertId();

    $link = autoUrl('linked-accounts/auth/' . $id . '/' . $key);

    $subject = 'Confirm you want to link your account';
    $message = '<p>Do you want to link the following accounts?</p>';
    $message .= '<ul><li>' . htmlspecialchars($_SESSION['EmailAddress']) . '</li><li>' . htmlspecialchars($user['EmailAddress']) . '</li></ul>';
    $message .= '<p><a href="' . $link . '">Click here to link your accounts</a></p>';
    $message .= '<p>Or follow this link <a href="' . $link . '">' . $link . '</a></p>';
    notifySend(null, $subject, $message, $user['Forename'] . ' ' . $user['Surname'], $user['EmailAddress']);
    $_SESSION['LinkedUserSuccess'] = true;
  }
} else {
  $_SESSION['LinkedUserIncorrectDetails'] = true;
}

if ($_SESSION['LinkedUserIncorrectDetails']) {
  header("Location: " . autoUrl("my-account/linked-accounts/new"));
} else {
  header("Location: " . autoUrl("my-account/linked-accounts"));
}