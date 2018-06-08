<?php
$galaName = $length = $venue = $closingDate = $lastDate = $galaFeeConstant = $galaFee = $hyTek = $added = "";
$added = false;
$content = "";

if (!empty($_POST['galaname'])) {
  $galaName = mysqli_real_escape_string($link, ucwords(trim(htmlspecialchars($_POST['galaname']))));
}

if (!empty($_POST['length'])) {
  $length = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['length'])));
}

if (!empty($_POST['venue'])) {
  $venue = mysqli_real_escape_string($link, ucwords(trim(htmlspecialchars($_POST['venue']))));
}

if (!empty($_POST['closingDate'])) {
  $closingDate = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['closingDate'])));
}

if (!empty($_POST['lastDate'])) {
  $lastDate = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['lastDate'])));
}

if (!empty($_POST['galaFeeConstant'])) {
  $galaFeeConstant = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaFeeConstant'])));
}

if ($galaFeeConstant == 1) {
  if (!empty($_POST['galaFee'])) {
    $galaFee = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaFee'])));
  }
}
else {
  $galaFeeConstant = 0;
  $galaFee = null;
}

if (!empty($_POST['HyTek'])) {
  $hyTek = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['HyTek'])));
}

if ($hyTek != 1) {
  $hyTek = 0;
}
//$sql = "INSERT INTO `galas` (`GalaName`, `CourseLength`, `GalaVenue`, `ClosingDate`, `GalaDate`, `GalaFeeConstant`, `GalaFee`, `HyTek`) VALUES ('$galaName', '$length', '$venue', '$closingDate', '$lastDate', '$galaFeeConstant', '$galaFee', '$hyTek');";
//echo $sql;
if ($galaName != null && $length != null && $venue != null && $closingDate != null && $lastDate != null && $galaFeeConstant != null) {
  $sql = "INSERT INTO `galas` (`GalaName`, `CourseLength`, `GalaVenue`, `ClosingDate`, `GalaDate`, `GalaFeeConstant`, `GalaFee`, `HyTek`) VALUES ('$galaName', '$length', '$venue', '$closingDate', '$lastDate', '$galaFeeConstant', '$galaFee', '$hyTek');";
  $action = mysqli_query($link, $sql);
  if ($action) {
    $added = true;
  }
}

if ($added) {
  $pagetitle = $title = "Gala Added";
  $content = "<p class=\"lead\">You have successfully added " . $galaName . " to the database.</p>";
  $content .= "<p>It will be open for entries from parents until " . date('j F Y', strtotime($closingDate)) . " and stay visible to all users until " . date('j F Y', strtotime($lastDate)) . "</p>";
  if ($galaFeeConstant == 1) {
    $content .= "<p>The fee for each swim is &pound;" . number_format($galaFee,2,'.','') . "</p>";
  }
  $content .= "<p><a href=\"../galas\" class=\"btn btn-outline-dark\">Return to Galas</a> <a href=\"addgala\" class=\"btn btn-outline-dark\">Add another</a></p>";

  // Send tweets via twitter
  // Get config for authentication data
  require_once BASE_PATH . 'config.php';
  // ENTER HERE YOUR CREDENTIALS (see readme.txt)
  $twitter = new Twitter($twitterConsumerKey, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret);
  try {
  	$tweet = $twitter->send($galaName . ' is now available to enter online at ' . autoUrl("")); // you can add $imagePath or array of image paths as second argument
  } catch (TwitterException $e) {
  	echo 'Error: ' . $e->getMessage();
  }
}
else {
  $pagetitle = $title = "An error occurred";
  $content = "<div class=\"alert alert-warning\"><strong>An error occurred</strong> <br>We could not add your gala.</div>";
  $content .= "<p><a href=\"../galas\" class=\"btn btn-outline-dark\">Return to Galas</a> <a href=\"addgala\" class=\"btn btn-outline-dark\">Try again</a></p>";
}
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
?>
