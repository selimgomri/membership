<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

use function GuzzleHttp\json_encode;

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;
$numFormat = new NumberFormatter("en", NumberFormatter::SPELLOUT);

$getRenewal = $db->prepare("SELECT renewalData.ID, renewalPeriods.ID PID, renewalPeriods.Name, renewalPeriods.Year, renewalData.User, renewalData.Document FROM renewalData LEFT JOIN renewalPeriods ON renewalPeriods.ID = renewalData.Renewal LEFT JOIN users ON users.UserID = renewalData.User WHERE renewalData.ID = ? AND users.Tenant = ?");
$getRenewal->execute([
  $id,
  $tenant->getId(),
]);
$renewal = $getRenewal->fetch(PDO::FETCH_ASSOC);

if (!$renewal) {
  halt(404);
}

if (!$user->hasPermission('Admin') && $renewal['User'] != $user->getId()) {
  halt(404);
}

$ren = Renewal::getUserRenewal($id);

$jsonResponse = [
  'status' => 200,
  'error' => null,
];

$goAhead = true;

$contact = new EmergencyContact();
$contact->connect($db);
if (isset($_POST['contact-id']) && isset($_POST['contact-name']) && mb_strlen(trim($_POST['contact-name'])) > 0 && isset($_POST['contact-number']) && mb_strlen(trim($_POST['contact-number'])) > 0) {
  $contact->getByContactID($_POST['contact-id']);

  if ($contact->getUserID() != $ren->getUser()) {
    $goAhead = false;
  }

  if ($goAhead) {
    $contact->setName(trim($_POST['contact-name']));

    if (isset($_POST['contact-relation'])) {
      $contact->setRelation(trim($_POST['contact-relation']));
    }

    try {
      $contact->setContactNumber(trim($_POST['contact-number']));
    } catch (Exception $e) {
      $jsonResponse = [
        'status' => 500,
        'error' => $e->getMessage(),
      ];
    }
  } else {
    $jsonResponse = [
      'status' => 500,
      'error' => 'Contact not found',
    ];
  }
} else {
  $jsonResponse = [
    'status' => 500,
    'error' => 'You must provide all fields',
  ];
}

header("content-type: application/json");
echo json_encode($jsonResponse);
