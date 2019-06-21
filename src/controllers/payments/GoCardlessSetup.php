<?php

// Setup GoCardless Client
include BASE_PATH . "config.php";
$client = null;
try {
  if (GOCARDLESS_USE_SANDBOX) {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> GOCARDLESS_SANDBOX_ACCESS_TOKEN,
      'environment' 		=> \GoCardlessPro\Environment::SANDBOX
    ]);
  } else {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> GOCARDLESS_ACCESS_TOKEN,
      'environment' 		=> \GoCardlessPro\Environment::LIVE
    ]);
  }
} catch (Exception $e) {
  halt(902);
}