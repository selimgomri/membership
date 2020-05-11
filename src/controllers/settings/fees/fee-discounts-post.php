<?php

$db = app()->db;
$systemInfo = app()->system;

try {

  $array = [
    'CLUB' => [],
    'ASA'  => [],
  ];

  for ($m = 1; $m <= 12; $m++) {
    $month =  mktime(0, 0, 0, $m, 1);
    if (isset($_POST['se-' . date('m', $month)]) && $_POST['se-' . date('m', $month)] >= 0 && $_POST['se-' . date('m', $month)] <= 100) {
      $array['ASA'] += [date('m', $month) => (int) $_POST['se-' . date('m', $month)]];
    } else {
      $array['ASA'] += [date('m', $month) => 0];
    }

    if (isset($_POST['club-' . date('m', $month)]) && $_POST['club-' . date('m', $month)] >= 0 && $_POST['club-' . date('m', $month)] <= 100) {
      $array['CLUB'] += [date('m', $month) => (int) $_POST['club-' . date('m', $month)]];
    } else {
      $array['CLUB'] += [date('m', $month) => 0];
    }
  }

  app()->tenant->setKey('MembershipDiscounts', json_encode($array));
  $_SESSION['Update-Success'] = true;
} catch (Exception $e) {
  $_SESSION['Update-Error'] = true;
}

header("Location: " . autoUrl("settings/fees/membership-discounts"));