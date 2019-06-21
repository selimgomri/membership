<?php

$user = $_SESSION['UserId'];
$pagetitle = "Payments Administration";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
  <div class="row">
		<div class="col-md-8">
			<h1>Create a Manual Charge</h1>
			<?php if (isset($_SESSION['ErrorState'])) {
				echo $_SESSION['ErrorState'];
				unset($_SESSION['ErrorState']);
			} ?>
			<form method="post">
				<h2>Select Parent</h2>
				<div class="form-row">
					<div class="col">
						<div class="form-group">
							<label for="user-surname">Search for User</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text">Surname</div>
								</div>
								<input type="text" class="form-control" id="user-surname">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group">
							<label for="user">Select a User</label>
							<select class="custom-select" name="user" id="user">
								<option selected>Select</option>
								<option>Search for a parent or enable JS first</option>
							</select>
						</div>
					</div>
				</div>
				<h2>Create Payment</h2>
				<div class="form-row">
					<div class="col">
						<div class="form-group">
							<label for="amount">Amount</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text mono">&pound;</div>
								</div>
								<input type="text" class="form-control mono" id="amount" name="amount" placeholder="Amount">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group">
							<label for="desc">Description</label>
							<input type="text" class="form-control" id="desc" name="desc" placeholder="Description" required>
						</div>
					</div>
				</div>
				<p class="mb-0"><button class="btn btn-success" type="submit">Charge</button></p>
			</form>
		</div>
  </div>
</div>

<script>
function getResult() {
  var user = document.getElementById("user-surname");
  var usersur = user.value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("user").innerHTML = this.responseText;
      }
    }
    xhttp.open("POST", "<?php echo autoURL("users/ajax/username"); ?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("userSur=" + usersur);
    console.log("Sent");
}
getResult();
document.getElementById("user-surname").oninput=getResult;
</script>

<?php include BASE_PATH . "views/footer.php";
