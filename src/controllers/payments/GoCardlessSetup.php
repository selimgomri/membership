<?php

// Setup GoCardless Client
include BASE_PATH . "config.php";
$client = new \GoCardlessPro\Client([
	'access_token' 		=> $gcAccessToken,
	'environment' 		=> \GoCardlessPro\Environment::SANDBOX
]);
