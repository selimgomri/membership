<?php

// Setup GoCardless Client

$client = null;
try {
  if (bool(app()->tenant->getKey('GOCARDLESS_USE_SANDBOX'))) {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> app()->tenant->getKey('GOCARDLESS_SANDBOX_ACCESS_TOKEN'),
      'environment' 		=> \GoCardlessPro\Environment::SANDBOX
    ]);
  } else {
    $client = new \GoCardlessPro\Client([
      'access_token' 		=> app()->tenant->getKey('GOCARDLESS_ACCESS_TOKEN'),
      'environment' 		=> \GoCardlessPro\Environment::LIVE
    ]);
  }
} catch (Exception $e) {
  halt(902);  
}