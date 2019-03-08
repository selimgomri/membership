<?php

// Validate all form data

use Respect\Validation\Validator as v;

$form_errors = [];
$from = $to = null;

if ($_POST['name'] == "" || $_POST['name'] == null) {
  $form_errors['name'] = "You did not provide a name for the qualification";
}

if (($_POST['valid-from'] == "" || $_POST['valid-from'] == null) || !v::date()->validate($_POST['valid-from'])) {
  $form_errors['valid-from'] = "You did not provide a valid from date";
} else {
  $from = date("Y-m-d", strtotime($_POST['valid-from']));
}

if ($_POST['expires']) {
  if (($_POST['valid-to'] != "" && $_POST['valid-to'] != null) || !v::date()->validate($_POST['valid-to'])) {
    $form_errors['valid-to'] = "You did not provide a valid to date";
  } else {
    $to = date("Y-m-d", strtotime($_POST['valid-to']));
  }
}

if (sizeof($form_errors) > 0) {
  // There was a problem, so send back to check details

  $_SESSION['NewQualificationData'] = $_POST;
  header("Location: " . app('request')->curl);
} else {
  // Otherwise insert into the database

  global $db;

  $add = $db->prepare("UPDATE qualifications SET `Name` = ?, Info = ?, `From` = ?, `To` = ?) WHERE ID = ?");

  try {
    $add->execute([$_POST['name'], $_POST['info'], $from, $to, $id]);
  } catch (Exception $e) {
    halt(500);
  }
  header("Location: " . app('request')->curl);
}
