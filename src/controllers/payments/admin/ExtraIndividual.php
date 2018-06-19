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
  <div class="row">
    <div class="col-md-6">
      <div id="output">
        <div class="ajaxPlaceholder">
          <span class="h1 d-block">
            <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>
            <br>Loading Content
          </span>If content does not display, please turn on JavaScript
        </div>
      </div>
    </div>
    <div class="col">
      <div class="my-3 p-3 bg-white rounded box-shadow">
        <form>
          <button type="submit" class="btn btn-dark">
            Add Swimmer to Extra
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function getResult() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log("We got here");
        document.getElementById("output").innerHTML = this.responseText;
        console.log(this.responseText);
      }
    }
    xhttp.open("POST", "<?php echo autoUrl("payments/extrafees/ajax/" . $id); ?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("");
    console.log("Sent");
}
// Call getResult immediately
getResult();
</script>

<?php include BASE_PATH . "views/footer.php";
