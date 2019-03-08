<?php

global $db;

// Validate all form data

$form_errors = [];
$from = $to = null;

if ($_POST['name'] == "" || $_POST['name'] == null) {
  $form_errors['name'] = "You did not provide a name for the qualification";
}

if (sizeof($form_errors) > 0) {
  // There was a problem, so send back to check details

  $_SESSION['EditQualificationData'] = $_POST;
  header("Location: " . app('request')->curl);

} else {
  // Otherwise insert into the database

  global $db;

  $add = $db->prepare("UPDATE qualificationsAvailable SET `Name` = ? WHERE ID = ?");

  try {
    $add->execute([$_POST['name'], $id]);
  } catch (Exception $e) {
    halt(500);
  }
  $_SESSION['EditQualificationSuccess'] = true;
  header("Location: " . app('request')->curl);

}
