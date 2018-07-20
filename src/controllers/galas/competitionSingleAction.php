<?php

use Respect\Validation\Validator as v;

$status = true;
$statusInfo = "";

$id = mysqli_real_escape_string($link, $id);
$content = "";
$galaID = $galaName = $courseLength = $galaVenue = $closingDate = $galaDate = $galaFee = "";

$galaFeeConstant = $hyTek = 0;

if (!empty($_POST['galaID'])) {
  $galaID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaID'])));
  if ($galaID != $id) {
    halt(500);
  }
}
if (!empty($_POST['galaname'])) {
  $galaName = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaname'])));
}
if (!empty($_POST['length'])) {
  $courseLength = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['length'])));
}
if (!empty($_POST['venue'])) {
  $galaVenue = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['venue'])));
}

if (!empty($_POST['closingDate']) && v::date()->validate($_POST['closingDate'])) {
  $date = strtotime($_POST['closingDate']);
  $closingDate = mysqli_real_escape_string($link, date("Y-m-d", $date));
} else {
  $status = false;
  $statusInfo .= "<li>The closing date was malformed and not understood clearly by the system</li>";
}

if (!empty($_POST['galaDate'])  && v::date()->validate($_POST['galaDate'])) {
  $date = strtotime($_POST['galaDate']);
  $galaDate = mysqli_real_escape_string($link, date("Y-m-d", $date));
} else {
  $status = false;
  $statusInfo .= "<li>The gala date was malformed and not understood clearly by the system</li>";
}

if (isset($_POST['GalaFeeConstant']) && $_POST['GalaFeeConstant'] == 1) {
  $galaFeeConstant = 1;
}
if (!empty($_POST['galaFee'])) {
  $galaFee = mysqli_real_escape_string($link, number_format(trim(htmlspecialchars($_POST['galaFee'])),2,'.',''));
}
if (isset($_POST['HyTek']) && $_POST['HyTek'] == 1) {
  $hyTek = 1;
}
if ($galaFeeConstant == 0) {
  $galaFee = 0.00;
}

if ($status) {
  $sql = "UPDATE `galas` SET  GalaName = '$galaName', CourseLength = '$courseLength', GalaVenue = '$galaVenue', ClosingDate = '$closingDate', GalaDate = '$galaDate', GalaFeeConstant = '$galaFeeConstant', GalaFee = '$galaFee', HyTek = '$hyTek' WHERE GalaID = '$galaID' ;";
  //$action = mysqli_query($link, $sql);
  if (mysqli_query($link, $sql)) {
    header("location: " . autoUrl("galas/competitions/" . $galaID) . "");
  }
}
else {
  $_SESSION['ErrorState'] = '
  <div class="alert alert-danger">
  <p class="mb-0">
  <strong>We were unable to update this gala</strong>
  </p>
  <p>The issue was</p>
  <ul class="mb-0">
  ' . $statusInfo . '
  </ul>
  </div>';
  header("location: " . autoUrl("galas/competitions/" . $galaID) . "");
}
