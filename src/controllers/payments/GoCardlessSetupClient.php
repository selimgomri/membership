<?php

$at = app()->tenant->getKey('GOCARDLESS_ACCESS_TOKEN');

$client = null;

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