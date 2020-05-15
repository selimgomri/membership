<?php

// Setup GoCardless Client

$at = app()->tenant->getGoCardlessAccessToken();

$client = null;
try {
  if (bool(env('IS_DEV'))) {
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
