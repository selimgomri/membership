<?php

use function GuzzleHttp\json_encode;

$jsonResponse = [
  'status' => 200,
  'html' => null,
];

include 'view.php';
try {
  ob_start();
  getView($id);
  $html = ob_get_clean();
  $jsonResponse = [
    'status' => 200,
    'html' => $html,
  ];
} catch (Exception $e) {
  $jsonResponse = [
    'status' => 500,
    'html' => null,
  ];
}

header('content-type: application/json');
echo json_encode($jsonResponse);
