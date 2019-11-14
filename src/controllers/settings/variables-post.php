<?php

global $db;
global $systemInfo;

$clubs = [];
$row = 1;
if (($handle = fopen(BASE_PATH . "includes/regions/clubs.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000)) !== false) {
    if ($row > 1) {
      $clubs += [$data[1] => [
        'Name' => $data[0],
        'Code' => $data[1],
        'District' => $data[2],
        'County' => $data[3],
        'MeetName' => $data[4],
      ]];
    }
    $row++;
  }
  fclose($handle);
}

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
  'SYSTEM_COLOUR' => null,
  'STRIPE' => null,
  'STRIPE_PUBLISHABLE' => null,
  'STRIPE_APPLE_PAY_DOMAIN' => null,
];

try {
  foreach ($vars as $key => $value) {
    if (isset($_POST[$key]) && $_POST[$key] != null && !$systemInfo->isExistingEnvVar($key)) {
      $systemInfo->setSystemOption($key, $_POST[$key]);
    }
  }

  if (isset($_POST['CLUB_INFO']) && env('ASA_CLUB_CODE') != $_POST['CLUB_INFO']) {
    // Update CLUB DATA
    if (!$systemInfo->isExistingEnvVar('CLUB_NAME')) {
      $systemInfo->setSystemOption('CLUB_NAME', $clubs[$_POST['CLUB_INFO']]['Name']);
    }
    if (!$systemInfo->isExistingEnvVar('CLUB_SHORT_NAME')) {
      $systemInfo->setSystemOption('CLUB_SHORT_NAME', $clubs[$_POST['CLUB_INFO']]['MeetName']);
    }
    if (!$systemInfo->isExistingEnvVar('ASA_CLUB_CODE')) {
      $systemInfo->setSystemOption('ASA_CLUB_CODE', $clubs[$_POST['CLUB_INFO']]['Code']);
    }
    if (!$systemInfo->isExistingEnvVar('ASA_DISTRICT')) {
      $systemInfo->setSystemOption('ASA_DISTRICT', $clubs[$_POST['CLUB_INFO']]['District']);
    }
    if (!$systemInfo->isExistingEnvVar('ASA_COUNTY')) {
      $systemInfo->setSystemOption('ASA_COUNTY', $clubs[$_POST['CLUB_INFO']]['County']);
    }
  }

  if (!$systemInfo->isExistingEnvVar('HIDE_MEMBER_ATTENDANCE')) {
    $hide = 1;
    if (bool($_POST['HIDE_MEMBER_ATTENDANCE'])) {
      $hide = 0;
    }
    $systemInfo->setSystemOption('HIDE_MEMBER_ATTENDANCE', $hide);
  }

  $vars['CLUB_ADDRESS'] = null;
  if (!$systemInfo->isExistingEnvVar('CLUB_ADDRESS')) {
    $addr = $_POST['CLUB_ADDRESS'];
    $addr = str_replace("\r\n", "\n", $addr);
    $addr = explode("\n", $addr);
    $addr = json_encode($addr);
    $systemInfo->setSystemOption('CLUB_ADDRESS', $addr);
  }

  $_SESSION['PCC-SAVED'] = true;
} catch (Exception $e) {
  pre($e);
  exit();
  $_SESSION['PCC-ERROR'] = true;
}

header("Location: " . currentUrl());