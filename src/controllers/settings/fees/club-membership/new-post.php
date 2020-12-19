<?php

$db = app()->db;
$tenant = app()->tenant;

try {

  if (!\SCDS\CSRF::verify()) {
    throw new Exception('Invalid CSRF token');
  }

  $insert = $db->prepare("INSERT INTO `clubMembershipClasses` (`ID`, `Name`, `Description`, `Fees`, `Tenant`) VALUES (?, ?, ?, ?, ?)");

  $description = null;
  if (isset($_POST['class-description']) && mb_strlen(trim($_POST['class-description'])) > 0) {
    $description = trim($_POST['class-description']);
  }

  $newObject = [
    'type' => 'NSwimmers',
    'upgrade_type' => 'TopUp',
    'fees' => [],
  ];

  $json = json_encode($newObject);

  $insert->execute([
    Ramsey\Uuid\Uuid::uuid4()->toString(),
    mb_convert_case(trim($_POST['class-name']), MB_CASE_TITLE),
    $description,
    $json,
    $tenant->getId(),
  ]);

} catch (Exception $e) {

}