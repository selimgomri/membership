<?php

$db = app()->db;

// Validate all form data

$form_errors = [];
$from = $to = null;

if ($_POST['name'] == "" || $_POST['name'] == null) {
  $form_errors['name'] = "You did not provide a name for the qualification";
}

if (sizeof($form_errors) > 0) {
  // There was a problem, so send back to check details

  $_SESSION['NewQualificationData'] = $_POST;
  header("Location: " . currentUrl());

} else {
  // Otherwise insert into the database

  $db = app()->db;

  $add = $db->prepare("INSERT INTO qualificationsAvailable (`Name`) VALUES (?)");

  try {
    $add->execute([trim($_POST['name'])]);
  } catch (Exception $e) {
    halt(500);
  }
  $_SESSION['AddedNewOption'] = true;
  header("Location: " . autoUrl("admin/qualifications"));

}
