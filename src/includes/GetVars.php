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
  'CLUB_ADDRESS' => null,
  'SYSTEM_COLOUR' => '#007bff',
];

try {
  foreach ($vars as $key => $value) {
    if (env($key) == null) {
      $v = $systemInfo->getSystemOption($key);
      if ($v != null) {
        if (!defined($key)) {
          define($key, $v);
        }
        putenv($key . "=" . $v);
      }
    } else {
      $systemInfo->setExistingEnvVar($key);
    }
  }
} catch (Exception $e) {
  halt(500);
}