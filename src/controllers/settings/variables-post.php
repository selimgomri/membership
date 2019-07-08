<?php

global $db;
global $systemInfo;

$vars = [
  'CLUB_NAME' => null,
  'CLUB_SHORT_NAME' => null,
  'ASA_CLUB_CODE' => null,
  'CLUB_EMAIL' => null,
  'CLUB_TRIAL_EMAIL' => null,
  'EMAIL_DOMAIN' => null,
  'CLUB_WEBSITE' => null,
  'SENDGRID_API_KEY' => null,
  'GOCARDLESS_USE_SANDBOX' => null,
  'GOCARDLESS_SANDBOX_ACCESS_TOKEN' => null,
  'GOCARDLESS_ACCESS_TOKEN' => null,
  'GOCARDLESS_WEBHOOK_KEY' => null,
];

try {
  foreach ($vars as $key => $value) {
    if (!$systemInfo->isExistingEnvVar($key)) {
      $systemInfo->setSystemOption($key, $_POST[$key]);
    }
  }
  $_SESSION['PCC-SAVED'] = true;
} catch (Exception $e) {
  $_SESSION['PCC-ERROR'] = true;
}

header("Location: " . currentUrl());