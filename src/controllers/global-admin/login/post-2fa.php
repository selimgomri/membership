<?php

$db = app()->db;

if (!isset($_SESSION['SCDS-SU-Login2FA']) || isset($_SESSION['SCDS-SuperUser'])) {
  halt(404);
}

$security_status = true;
if ($_POST['SessionSecurity'] != session_id()) {
  $security_status = false;
}

if (!SCDS\CSRF::verify()) {
  $security_status = false;
}

use PragmaRX\Google2FA\Google2FA;
$ga2fa = new Google2FA();

http_response_code(302);

try {

  if (!$security_status) {
    throw new Exception('CSRF Error');
  }

  if ($ga2fa->verifyKey($_SESSION['SCDS-SU-Login2FA']['TwoFactorHash'], $_POST['auth'])) {
    $_SESSION['SCDS-SuperUser'] = $_SESSION['SCDS-SU-Login2FA']['User'];
    unset($_SESSION['SCDS-SU-Login2FA']);
    header("location: " . autoUrl('admin'));
  } else {
    header("location: " . autoUrl('admin/login'));
  }

} catch (Exception $e) {

  header("location: " . autoUrl('admin/login'));

}