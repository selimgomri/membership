<?php

$db = app()->db;
$tenant = app()->tenant;

try {

  if (!\SCDS\CSRF::verify()) {
    throw new Exception('Invalid CSRF token');
  }

  if (isset($_POST['default-class'])) {
    // Validate
    $getClass = $db->prepare("SELECT `ID`, `Name`, `Description`, `Fees` FROM `clubMembershipClasses` WHERE `ID` = ? AND `Tenant` = ?");
    $getClass->execute([
      $_POST['default-class'],
      $tenant->getId(),
    ]);
    $class = $getClass->fetch(PDO::FETCH_ASSOC);

    if (!$class) {
      throw new Exception('Membership class not found at this tenant');
    }

    $tenant->setKey('DEFAULT_MEMBERSHIP_CLASS', $_POST['default-class']);
  }
} catch (Exception $e) {

}

http_response_code(302);
header('location: ' . autoUrl('settings/fees/membership-fees'));
