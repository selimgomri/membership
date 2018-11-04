<?php

// Setup GoCardless Client
include BASE_PATH . "config.php";
$client = new \GoCardlessPro\Client([
	'access_token' 		=> GOCARDLESS_ACCESS_TOKEN,
	'environment' 		=> \GoCardlessPro\Environment::LIVE
]);
