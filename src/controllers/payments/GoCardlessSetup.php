<?php

// Setup GoCardless Client
include BASE_PATH . "config.php";
$client = null;
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
