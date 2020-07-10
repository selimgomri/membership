<?php

// Setup GoCardless Client

// $at = app()->tenant->getGoCardlessAccessToken();
$client = null;
try {
  include 'GoCardlessSetupClient.php';
} catch (Exception $e) {
  halt(902);
}
