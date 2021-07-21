<?php

$db = app()->db;
$tenant = app()->tenant;

$getClass = $db->prepare("SELECT `ID`, `Name`, `Description`, `Fees`, `Type` FROM `clubMembershipClasses` WHERE `ID` = ? AND `Tenant` = ?");
$getClass->execute([
  $id,
  $tenant->getId(),
]);
$class = $getClass->fetch(PDO::FETCH_ASSOC);

if (!$class) {
  halt(404);
}

try {

  if (!\SCDS\CSRF::verify()) {
    throw new Exception('Invalid CSRF token');
  }

  $update = $db->prepare("UPDATE `clubMembershipClasses` SET `Name` = ?, `Description` = ?, `Fees` = ? WHERE `ID` = ?");

  $description = null;
  if (isset($_POST['class-description']) && mb_strlen(trim($_POST['class-description'])) > 0) {
    $description = trim($_POST['class-description']);
  }

  $type = null;
  $types = ['NSwimmers', 'PerPerson'];
  if (isset($_POST['class-fee-type']) && in_array($_POST['class-fee-type'], $types)) {
    $type = $_POST['class-fee-type'];
  } else {
    throw new Exception('Invalid type or type not provided');
  }

  if ($class['Type'] == 'national_governing_body' && $_POST['class-fee-type'] != 'PerPerson') {
    // They've messed about with it - throw an error
    throw new Exception('Invalid type for NGB class');
  }

  $upgrade = 'TopUp';

  $fees = [];
  if ($type == 'PerPerson' && isset($_POST['class-price'])) {
    $fees = [\Brick\Math\BigDecimal::of((string) $_POST['class-price'])->withPointMovedRight(2)->toInt()];
  } else if ($type == 'NSwimmers' && isset($_POST['class_fee'])) {
    foreach ($_POST['class_fee'] as $fee) {
      $fees[] = \Brick\Math\BigDecimal::of((string) $fee)->withPointMovedRight(2)->toInt();
    }
  }

  $newObject = [
    'type' => $type,
    'upgrade_type' => $upgrade,
    'fees' => $fees,
  ];

  $json = json_encode($newObject);

  $update->execute([
    mb_convert_case(trim($_POST['class-name']), MB_CASE_TITLE),
    $description,
    $json,
    $id,
  ]);
} catch (Exception $e) {
}

http_response_code(302);
header('location: ' . autoUrl('settings/fees/membership-fees/' . $id));
