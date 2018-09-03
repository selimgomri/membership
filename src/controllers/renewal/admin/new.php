<?php

$pagetitle = "Create New Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";

$val = null;
?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded shadow">
		<form method="post">
			<h1>Create a new Renewal Period</h1>
			<? if (isset($_SESSION['NewRenewalErrorInfo'])) {
				echo $_SESSION['NewRenewalErrorInfo'];
				unset($_SESSION['NewRenewalErrorInfo']);
				$val = $_SESSION['NewRenewalForm'];
				unset($_SESSION['NewRenewalForm']);
			} ?>

			<div class="form-group">
		    <label for="name">Renewal Name</label>
		    <input type="text" class="form-control" id="name" name="name" placeholder="For 2018" value="<? echo $val[0]; ?>">
		  </div>

			<div class="form-row">
				<div class="form-group col-md-6">
			    <label for="start">Start Date</label>
			    <input type="date" class="form-control" id="start" name="start" value="<? echo date("Y-m-d"); ?>" value="<? echo $val[1]; ?>">
			  </div>

				<div class="form-group col-md-6">
			    <label for="end">End Date</label>
			    <input type="date" class="form-control" id="end" name="end" value="<? echo date("Y-m-d"); ?>" value="<? echo $val[2]; ?>">
			  </div>
			</div>

			<p class="mb-0">
				<button class="btn btn-success" type="submit">
					Add Renewal
				</button>
			</p>

		</form>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
