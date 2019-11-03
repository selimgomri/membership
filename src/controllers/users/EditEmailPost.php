<?php

if (!SCDS\CSRF::verify()) {
  halt(403);
}

global $db;

use Respect\Validation\Validator as v;

$email = mb_strtolower(trim($_POST['new-user-email']));

if (!v::email()->validate($email)) {
  $_SESSION['User-Update-Email-Error'] = true;
} else {
  // Update user email
  try {
    $update = $db->prepare("UPDATE users SET EmailAddress = ? WHERE UserID = ?");
    $update->execute([$email, $id]);
    $_SESSION['User-Update-Email-Success'] = true;
  } catch (Exception $e) {
    $_SESSION['User-Update-Email-Error'] = true;
  }
}

header("Location: " . autoUrl("users/" . $id));