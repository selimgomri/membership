<?php
$id = mysqli_real_escape_string($link, $id);
$userID = $_SESSION['UserID'];
$pagetitle = "Medical Review";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Medical Form</h1>
	<p class="lead">Check the details for {<? echo $id; ?>} are correct.</p>

	<div class="mb-2">
		<p class="mb-2">Does your child have any specific medical conditions or disabilities?</p>

		<div class="custom-control custom-radio">
		  <input type="radio" id="medConDisNo" name="medConDis" class="custom-control-input">
		  <label class="custom-control-label" for="medConDisNo">No</label>
		</div>
		<div class="custom-control custom-radio">
		  <input type="radio" id="medConDisYes" name="medConDis" class="custom-control-input">
		  <label class="custom-control-label" for="medConDisYes">Yes</label>
		</div>
	</div>

	<div class="form-group">
    <label for="medConDisDetails">If yes give details</label>
    <textarea class="form-control" id="medConDisDetails" name="medConDisDetails" rows="3"></textarea>
  </div>

	<!-- -->

	<div class="mb-2">
		<p class="mb-2">Does your child have any allergies?</p>

		<div class="custom-control custom-radio">
		  <input type="radio" id="allergiesNo" name="allergies" class="custom-control-input">
		  <label class="custom-control-label" for="allergiesNo">No</label>
		</div>
		<div class="custom-control custom-radio">
		  <input type="radio" id="allergiesYes" name="allergies" class="custom-control-input">
		  <label class="custom-control-label" for="allergiesYes">Yes</label>
		</div>
	</div>

	<div class="form-group">
    <label for="allergiesDetails">If yes give details</label>
    <textarea class="form-control" id="allergiesDetails" name="allergiesDetails" rows="3"></textarea>
  </div>

	<!-- -->

	<div class="mb-2">
		<p class="mb-2">Does your child take any regular medication?</p>

		<div class="custom-control custom-radio">
		  <input type="radio" id="medicineNo" name="medicine" class="custom-control-input">
		  <label class="custom-control-label" for="medicineNo">No</label>
		</div>
		<div class="custom-control custom-radio">
		  <input type="radio" id="medicineYes" name="medicine" class="custom-control-input">
		  <label class="custom-control-label" for="medicineYes">Yes</label>
		</div>
	</div>

	<div class="form-group">
    <label for="medConDisDetails">If yes give details</label>
    <textarea class="form-control" id="medicineDetails" name="medicineDetails" rows="3"></textarea>
  </div>

	<div class="mb-3">
		<a class="btn btn-outline-success" href="">Save</a>
		<a class="btn btn-success" href="">Save and Continue</a>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
