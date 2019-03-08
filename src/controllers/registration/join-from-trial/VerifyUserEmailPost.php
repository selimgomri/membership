<?php

if (trim($_POST['verify-code']) == $_SESSION['AC-Registration']['EmailConfirmationCode']) {
  // SUCCESS, continue to the terms and conditions
  $_SESSION['AC-Registration']['EmailConfirmationCode'] = "";
  unset($_SESSION['AC-Registration']['EmailConfirmationCode']);

  try {
    global $db;
    $query = $db->prepare("UPDATE joinParents SET Email = ? WHERE Hash = ?");
    $query->execute([$_SESSION['AC-UserDetails']['email-addr'], $_SESSION['AC-Registration']['Hash']]);

    $_SESSION['AC-Registration']['Stage'] = 'TermsConditions';
    header("Location: " . autoUrl("register/ac/terms-and-conditions"));
  } catch (Exception $e) {
    halt(500);
  }
} else {
  // The code entered was wrong, try again
  $_SESSION['AC-VerifyEmail-Error'] = true;
  header("Location: " . app('request')->curl);
}
