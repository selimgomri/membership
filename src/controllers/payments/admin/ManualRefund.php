<?php

$user = $_SESSION['UserId'];
$pagetitle = "Payments Administration";

$use_white_background = true;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<h1>Credit a User</h1>
  <div class="row">
    <div class="col-md-8">
      <p class="lead">
        Users can be refunded by adding a credit voucher to their bill. This
        amount will be taken off the total of their next payment.
      </p>

      <?php if (isset($_SESSION['ErrorState'])) {
        echo $_SESSION['ErrorState'];
        unset($_SESSION['ErrorState']);
      } ?>

      <form method="post">
    		<div class="form-group">
    	    <label for="user">User Identification Number</label>
    			<div class="input-group">
    				<div class="input-group-prepend">
    	        <div class="input-group-text mono">CLSU</div>
    	      </div>
    	  		<input type="number" class="form-control mono" id="user" name="user" aria-describedby="userHelp" placeholder="Enter number" required>
    			</div>
    	    <small id="userHelp" class="form-text text-muted">You can find a User ID in the <a target="_blank" href="<?php echo autoUrl("users"); ?>">Users section</a></small>
    	  </div>
    		<div class="form-group">
    			<p>You have selected - <span id="selectedUserName">No Parent Selected</span></p>
    		</div>
        <div class="form-group">
    	    <label for="desc">Description</label>
      		<input type="text" class="form-control" id="desc" name="desc" placeholder="Description" required>
    	  </div>
    	  <div class="form-group">
    	    <label for="amount">Amount</label>
    			<div class="input-group">
    				<div class="input-group-prepend">
    	        <div class="input-group-text mono">&pound;</div>
    	      </div>
    	    	<input type="text" class="form-control mono" id="amount" name="amount" placeholder="Amount">
    			</div>
    	  </div>
    		<p><button class="btn btn-success" type="submit">Credit</button></p>
    	</form>
    </div>
  </div>
</div>

<script>
function getResult() {
  var user = document.getElementById("user");
  var userID = user.value;
  console.log(userID);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log("We got here");
        document.getElementById("selectedUserName").innerHTML = this.responseText;
        console.log(this.responseText);
      }
    }
    xhttp.open("POST", "<?php echo autoURL("users/ajax/username"); ?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("userID=" + userID);
    console.log("Sent");
}
document.getElementById("user").oninput=getResult;
</script>

<?php include BASE_PATH . "views/footer.php";
