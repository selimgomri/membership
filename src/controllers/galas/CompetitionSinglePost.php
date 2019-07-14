<?php

global $db;

$hyTek = 0;

$galaName = $courseLength = $galaVenue = $closingDate = $galaDate = $galaFeeConstant = $galaFee = $coachEnters = 0;

if (!empty($_POST['galaname'])) {
  $galaName = trim($_POST['galaname']);
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
if (!empty($_POST['GalaFeeConstant'])) {
  $galaFeeConstant = $_POST['GalaFeeConstant'];
}
if (!empty($_POST['galaFee'])) {
  $galaFee = number_format($_POST['galaFee'],2,'.','');
}
if ($_POST['HyTek']) {
  $hyTek = 1;
}
if ($_POST['coachDecides']) {
  $coachEnters = 1;
}
if ($galaFeeConstant == 0 || $galaFeeConstant == null) {
  $galaFeeConstant = 0;
  $galaFee = 0.00;
}

try {
  $update  = $db->prepare("UPDATE `galas` SET  GalaName = ?, CourseLength = ?, GalaVenue = ?, ClosingDate = ?, GalaDate = ?, GalaFeeConstant = ?, GalaFee = ?, HyTek = ?, CoachEnters = ? WHERE GalaID = ?");
  $update->execute([
    $galaName,
    $courseLength,
    $galaVenue,
    $closingDate,
    $galaDate,
    $galaFeeConstant,
    $galaFee,
    $hyTek,
    $coachEnters,
    $id
  ]);
  header("location: " . autoUrl("galas/" . $id));
} catch (Exception $e) {
  halt(500);
  //pre($e);
}
