<?php

$db = app()->db;

if (isset($_SESSION['SCDS-SuperUser'])) {
  halt(404);
}

$getUser = $db->prepare("SELECT ID, Email, PWHash, TwoFactor FROM superUsers WHERE Email = ?");
$getUser->execute([
  mb_strtolower($_POST['email-address']),
]);
$user = $getUser->fetch(PDO::FETCH_ASSOC);

http_response_code(302);

try {

  if (!$user) {
    throw new Exception('Not Found');
  }

  if (!password_verify($_POST['password'], $user['PWHash'])) {
    throw new Exception('Not Found');
  }

  $_SESSION['SCDS-SU-Login2FA'] = [
    'User' => $user['ID'],
    'Email' => $user['Email'],
    'TwoFactorHash' => $user['TwoFactor']
  ];

  header('location: ' . autoUrl('admin/login'));

} catch (Exception $e) {

  $_SESSION['SCDS-SU-LoginError'] = true;

}

header('location: ' . autoUrl('admin/login'));