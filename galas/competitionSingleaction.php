<?php
$content = "";
$galaID = $galaName = $courseLength = $galaVenue = $closingDate = $galaDate = $galaFeeConstant = $galaFee = $hyTek = "";

if (!empty($_POST['galaID'])) {
  $galaID = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaID'])));
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
if (!empty($_POST['closingDate'])) {
  $closingDate = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['closingDate'])));
}
if (!empty($_POST['galaDate'])) {
  $galaDate = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaDate'])));
}
if (!empty($_POST['GalaFeeConstant'])) {
  $galaFeeConstant = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['GalaFeeConstant'])));
}
if (!empty($_POST['galaFee'])) {
  $galaFee = mysqli_real_escape_string($link, number_format(trim(htmlspecialchars($_POST['galaFee'])),2,'.',''));
}
if (!empty($_POST['HyTek'])) {
  $hyTek = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['HyTek'])));
}
if ($galaFeeConstant == 0 || $galaFeeConstant == null) {
  $galaFeeConstant = 0;
  $galaFee = 0.00;
}

if (isset($galaID)) {
  $sql = "UPDATE `galas` SET  GalaName = '$galaName', CourseLength = '$courseLength', GalaVenue = '$galaVenue', ClosingDate = '$closingDate', GalaDate = '$galaDate', GalaFeeConstant = '$galaFeeConstant', GalaFee = '$galaFee', HyTek = '$hyTek' WHERE GalaID = '$galaID' ;";
  $action = mysqli_query($link, $sql);
  if ($action) {
    header("location: " . autoUrl("galas/competitions/" . $galaID) . "");
  }
}
else {
  $pagetitle = $title = "An error occurred";
  $content = "<div class=\"alert alert-warning\"><strong>An error occurred</strong> <br>We could not edit this gala.</div>";
}
?>
