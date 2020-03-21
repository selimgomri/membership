<?php

$data = [
  'showMessage' => false,
  'message' => null
];

if (env('EMERGENCY_MESSAGE_TYPE') != 'NONE' && env('EMERGENCY_MESSAGE')) {
  $markdown = new ParsedownExtra();
  $message = "";

  $message .= '<div class="py-5 ';

  if (env('EMERGENCY_MESSAGE_TYPE') == 'SUCCESS') {
    $message .= 'bg-success text-white';
  }

  if (env('EMERGENCY_MESSAGE_TYPE') == 'DANGER') {
    $message .= 'bg-danger text-white';
  }

  if (env('EMERGENCY_MESSAGE_TYPE') == 'WARN') {
    $message .= 'bg-warning text-body';
  }

  $message .= '"><div class="container emergency-message">';
  try {
    $message .= $markdown->text(env('EMERGENCY_MESSAGE'));
  } catch (Exception $e) {
    $message .= '<p>An emergency message has been set but cannot be rendered.</p>';
  }
  $message .= '</div> </div>';

  $data = [
    'showMessage' => true,
    'message' => $message
  ];
}

header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
header("content-type: application/json");
echo json_encode($data);