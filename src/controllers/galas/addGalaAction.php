<?php

global $db;
use Respect\Validation\Validator as v;

$status = true;
$statusInfo = "";

$galaName = $length = $venue = $closingDate = $lastDate = $galaFee = $added = null;
$added = false;
$galaFeeConstant = $hyTek = 0;
$content = "";

if (!empty($_POST['galaname'])) {
  $galaName = mysqli_real_escape_string($link, ucwords(trim($_POST['galaname'])));
  if (strlen($galaName) == 0) {
    $status = false;
    $statusInfo .= "<li>No gala name was provided</li>";
  }
}

if (!empty($_POST['length'])) {
  $length = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['length'])));
  if (strlen($length) == 0) {
    $status = false;
    $statusInfo .= "<li>There was a problem with the supplied course length</li>";
  }
}

if (!empty($_POST['venue'])) {
  $venue = mysqli_real_escape_string($link, ucwords(trim(htmlspecialchars($_POST['venue']))));
  if (strlen($venue) == 0) {
    $status = false;
    $statusInfo .= "<li>You failed to supply a place name</li>";
  }
}

if (!empty($_POST['closingDate']) && v::date()->validate($_POST['closingDate'])) {
  $date = strtotime($_POST['closingDate']);
  $closingDate = mysqli_real_escape_string($link, date("Y-m-d", $date));
} else {
  $status = false;
  $statusInfo .= "<li>The closing date was malformed and not understood clearly by the system</li>";
}

if (!empty($_POST['lastDate']) && v::date()->validate($_POST['lastDate'])) {
  $date = strtotime($_POST['lastDate']);
  $lastDate = mysqli_real_escape_string($link, date("Y-m-d", $date));
} else {
  $status = false;
  $statusInfo .= "<li>The gala date was malformed and not understood clearly by the system</li>";
}

if (isset($_POST['galaFeeConstant']) && $_POST['galaFeeConstant'] == 1) {
  $galaFeeConstant = 1;
  if (!empty($_POST['galaFee'])) {
    $galaFee = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['galaFee'])));
  } else {
    $galaFee = 0.00;
  }
} else {
  $galaFee = 0.00;
}

if (isset($_POST['HyTek'])) {
  $hyTek = 1;
}

//$sql = "INSERT INTO `galas` (`GalaName`, `CourseLength`, `GalaVenue`, `ClosingDate`, `GalaDate`, `GalaFeeConstant`, `GalaFee`, `HyTek`) VALUES ('$galaName', '$length', '$venue', '$closingDate', '$lastDate', '$galaFeeConstant', '$galaFee', '$hyTek');";
//echo $sql;

if ($status) {
  try {
    $query = $db->prepare("INSERT INTO `galas` (`GalaName`, `CourseLength`, `GalaVenue`, `ClosingDate`, `GalaDate`, `GalaFeeConstant`, `GalaFee`, `HyTek`) VALUES (?, ?, ?, ?,?, ?, ?, ?)");
    $query->execute([$galaName, $length, $venue, $closingDate, $lastDate, $galaFeeConstant, $galaFee, $hyTek]);
    $added = true;
  } catch (Exception $e) {
    $statusInfo .= "<li>Database error</li>";
  }
}

if ($added && $status) {
  $pagetitle = $title = "Gala Added";
  $content = "<p class=\"lead\">You have successfully added " . htmlspecialchars($galaName) . " to the database.</p>";
  $content .= "<p>It will be open for entries from parents until " . date('j F Y', strtotime($closingDate)) . " and stay visible to all users until " . date('j F Y', strtotime($lastDate)) . "</p>";
  if ($galaFeeConstant == 1) {
    $content .= "<p>The fee for each swim is &pound;" . number_format($galaFee,2,'.','') . "</p>";
  }
  $content .= "<p><a href=\"" . autoUrl("galas") . "\" class=\"btn
  btn-success rounded\">Return to Galas</a> <a href=\"" . autoUrl("galas/addgala") . "\"
  class=\"btn btn-outline-dark\">Add another</a></p>";

  if (defined('TWITTER_CONSUMER_KEY') && defined('TWITTER_CONSUMER_SECRET') &&
  defined('TWITTER_ACCESS_TOKEN') && defined('TWITTER_ACCESS_TOKEN_SECRET')) {
    // Send tweets via twitter
    // ENTER HERE YOUR CREDENTIALS (see readme.txt)
    $twitter = new Twitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
    try {
    	$tweet = $twitter->send($galaName . ' is now available to enter online at ' . autoUrl("")); // you can add $imagePath or array of image paths as second argument
    } catch (TwitterException $e) {
    	// Do nothing just assume there isn't an API key
    	// echo 'Error: ' . $e->getMessage();
    }
  }
}
else {
  $_SESSION['ErrorState'] = '
  <div class="alert alert-danger">
  <p class="mb-0">
  <strong>We were unable to add this gala</strong>
  </p>
  <p>The issue was</p>
  <ul class="mb-0">
  ' . $statusInfo . '
  </ul>
  </div>';
  header("location: " . autoUrl("galas/addgala"));
}
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add gala</li>
  </ol>
</nav>
<?php echo "<h1>" . $title . "</h1>";
echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
