<?php

// Setup GoCardless Client

$client = null;
try {
  if (env('GOCARDLESS_USE_SANDBOX')) {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> env('GOCARDLESS_SANDBOX_ACCESS_TOKEN'),
      'environment' 		=> \GoCardlessPro\Environment::SANDBOX
    ]);
  } else {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> env('GOCARDLESS_ACCESS_TOKEN'),
      'environment' 		=> \GoCardlessPro\Environment::LIVE
    ]);
  }
} catch (Exception $e) {
  halt(902);
}