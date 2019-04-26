<?php
$id = mysqli_real_escape_string($link, $id);
$content = "";
$galaID = $galaName = $courseLength = $galaVenue = $closingDate = $galaDate = $galaFeeConstant = $galaFee = $hyTek = "";

if (!empty($_POST['galaID'])) {
  $galaID = mysqli_real_escape_string($link, trim(($_POST['galaID'])));
  if ($galaID != $id) {
    halt(500);
  }
}
if (!empty($_POST['galaname'])) {
  $galaName = mysqli_real_escape_string($link, trim(($_POST['galaname'])));
}
if (!empty($_POST['length'])) {
  $courseLength = mysqli_real_escape_string($link, trim(($_POST['length'])));
}
if (!empty($_POST['venue'])) {
  $galaVenue = mysqli_real_escape_string($link, trim(($_POST['venue'])));
}
if (!empty($_POST['closingDate'])) {
  $closingDate = mysqli_real_escape_string($link, trim(($_POST['closingDate'])));
}
if (!empty($_POST['galaDate'])) {
  $galaDate = mysqli_real_escape_string($link, trim(($_POST['galaDate'])));
}
if (!empty($_POST['GalaFeeConstant'])) {
  $galaFeeConstant = mysqli_real_escape_string($link, trim(($_POST['GalaFeeConstant'])));
}
if (!empty($_POST['galaFee'])) {
  $galaFee = mysqli_real_escape_string($link, number_format(trim(($_POST['galaFee'])),2,'.',''));
}
if (!empty($_POST['HyTek'])) {
  $hyTek = mysqli_real_escape_string($link, trim(($_POST['HyTek'])));
}
if ($galaFeeConstant == 0 || $galaFeeConstant == null) {
  $galaFeeConstant = 0;
  $galaFee = 0.00;
}

if (isset($galaID)) {
  $sql = "UPDATE `galas` SET  GalaName = '$galaName', CourseLength = '$courseLength', GalaVenue = '$galaVenue', ClosingDate = '$closingDate', GalaDate = '$galaDate', GalaFeeConstant = '$galaFeeConstant', GalaFee = '$galaFee', HyTek = '$hyTek' WHERE GalaID = '$galaID' ;";
  //$action = mysqli_query($link, $sql);
  if (mysqli_query($link, $sql)) {
    header("location: " . autoUrl("galas/" . $galaID));
  }
}
else {
  $pagetitle = $title = "An error occurred";
  $content = "<div class=\"alert alert-warning\"><strong>An error occurred</strong> <br>We could not edit this gala.</div>";
}
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
?>
