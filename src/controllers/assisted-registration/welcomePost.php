<?php

$db = app()->db;

use Respect\Validation\Validator as v;

if (isset($_SESSION['AssRegUser']) && $_SESSION['AssRegUser']) {
  $_SESSION['AssRegUser'] = null;
  unset($_SESSION['AssRegUser']);
}

$getUserInfo = $db->prepare("SELECT AccessLevel FROM users WHERE EmailAddress = ?");

$email = trim(mb_strtolower($_POST['email-address']));
$getUserInfo->execute([$email]);
  
$status = true;
if (!v::email()->validate($email)) {
  $status = false;
}

$info = $getUserInfo->fetchColumn();

if ($status && $info == 'Parent') {
  $getUserId = $db->prepare("SELECT UserID FROM users WHERE EmailAddress = ?");
  $getUserId->execute([$email]);
  
  $_SESSION['AssRegUser'] = $getUserId->fetchColumn();
  $_SESSION['AssRegExisting'] = true;

  header("Location: " . autoUrl("assisted-registration/select-swimmers"));
} else if ($status && $info == null) {
  // USER DOES NOT EXIST
  $_SESSION['AssRegUserEmail'] = $email;
  header("Location: " . autoUrl("assisted-registration/start"));
} else if (!$status) {
  // INVALID EMAIL ADDRESS
  $_SESSION['AssRegEmailError'] = 'INV-EMAIL';
  header("Location: " . autoUrl("assisted-registration#get-started"));
} else {
  // NOT A PARENT ACCOUNT
  $_SESSION['AssRegEmailError'] = 'NOT-PARENT';
  header("Location: " . autoUrl("assisted-registration#get-started"));
}