<?php

// Setup GoCardless Client

// $at = app()->tenant->getGoCardlessAccessToken();
$at = app()->tenant->getKey('GOCARDLESS_ACCESS_TOKEN');

$client = null;
try {
  if (bool(getenv('IS_DEV'))) {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> $at,
      'environment' 		=> \GoCardlessPro\Environment::SANDBOX
    ]);
  } else {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> $at,
      'environment' 		=> \GoCardlessPro\Environment::LIVE
    ]);
  }
} catch (Exception $e) {
  halt(902);
}
