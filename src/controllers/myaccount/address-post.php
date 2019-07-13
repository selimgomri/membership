<?php

global $db;
global $currentUser;

try {
  if (isset($_POST['street-and-number']) && $_POST['street-and-number'] && isset($_POST['town-city']) && $_POST['town-city'] && isset($_POST['post-code']) && $_POST['post-code']) {
    $addr = [
      'streetAndNumber' => trim($_POST['street-and-number']),
      'city' => trim($_POST['town-city']),
      'postCode' => trim($_POST['post-code']),
    ];
    if (isset($_POST['flat-building']) && $_POST['flat-building']) {
      $addr += ['flatOrBuilding' => trim($_POST['flat-building'])];
    }
    $addr = json_encode($addr);
    $currentUser->setUserOption('MAIN_ADDRESS', $addr);
    $_SESSION['OptionsUpdate'] = true;
  }
} catch (Exception $e) {

}

header("Location: " . currentUrl());