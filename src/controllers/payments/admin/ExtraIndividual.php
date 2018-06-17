<?php

$id = mysqli_real_escape_string($link, $id);
$user = $_SESSION['UserID'];

$sql = "SELECT * FROM `extras` WHERE `ExtraID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) != 1) {
  halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$pagetitle = $row['ExtraName'] . " - Extras";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
  <div class="row align-items-center">
    <div class="col-md-6">
	    <h1><? echo $row['ExtraName']; ?></h1>
    </div>
    <div class="col text-right">
      <a href="" class="btn btn-dark">Edit</a>
      <a href="" class="btn btn-danger">Delete</a>
    </div>
  </div>
  <hr>
  <div id="output">
    <div class="ajaxPlaceholder">
      <span class="h1 d-block">
        <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>
        <br>Loading Content
      </span>If content does not display, please turn on JavaScript
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
