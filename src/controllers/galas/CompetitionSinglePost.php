<?php

global $db;

$hyTek = 0;

$galaName = $courseLength = $galaVenue = $closingDate = $galaDate = $galaFeeConstant = $galaFee = $coachEnters = $approvalNeeded = 0;
$description = "";

if (!empty($_POST['galaname'])) {
  $galaName = trim($_POST['galaname']);
}
if (!empty($_POST['description'])) {
  $description = trim($_POST['description']);
}
if (!empty($_POST['length'])) {
  $courseLength = $_POST['length'];
}
if (!empty($_POST['venue'])) {
  $galaVenue = trim($_POST['venue']);
}
if (!empty($_POST['closingDate'])) {
  $closingDate = $_POST['closingDate'];
}
if (!empty($_POST['galaDate'])) {
  $galaDate = $_POST['galaDate'];
}
$galaFeeConstant = $_POST['GalaFeeConstant'] = true;
if (isset($_POST['HyTek']) && bool($_POST['HyTek'])) {
  $hyTek = 1;
}
if (isset($_POST['coachDecides']) && bool($_POST['coachDecides'])) {
  $coachEnters = 1;
}
if (isset($_POST['approvalNeeded']) && bool($_POST['approvalNeeded'])) {
  $approvalNeeded = 1;
}
if ($galaFeeConstant == 0 || $galaFeeConstant == null) {
  $galaFeeConstant = 0;
  $galaFee = 0.00;
}

try {
  $update  = $db->prepare("UPDATE `galas` SET  GalaName = ?, `Description` = ?, CourseLength = ?, GalaVenue = ?, ClosingDate = ?, GalaDate = ?, HyTek = ?, CoachEnters = ?, RequiresApproval = ? WHERE GalaID = ?");
  $update->execute([
    $galaName,
    $description,
    $courseLength,
    $galaVenue,
    $closingDate,
    $galaDate,
    $hyTek,
    $coachEnters,
    $approvalNeeded,
    $id
  ]);
  header("location: " . autoUrl("galas/" . $id));
} catch (Exception $e) {
  halt(500);
  //pre($e);
}
