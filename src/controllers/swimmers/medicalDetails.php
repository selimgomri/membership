<?php
$id = mysqli_real_escape_string($link, $id);
$name = getSwimmerName($id);

$yes = $no = "";

$userID = $_SESSION['UserID'];
$pagetitle = $name . " - Medical Review";

$sql = "SELECT * FROM `members` LEFT JOIN `memberMedical` ON members.MemberID =
memberMedical.MemberID WHERE members.MemberID = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(500);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (($_SESSION['AccessLevel'] == "Parent" || $_SESSION['AccessLevel'] == "Galas" ||
$_SESSION['AccessLevel'] == "Coach") && $row['UserID'] != $_SESSION['UserID']) {
	halt(404);
}

include BASE_PATH . "views/header.php";
?>

<div class="container">
	<form method="post" action="<? echo app('request')->curl; ?>">
    <div class="row align-items-center">
      <div class="col-sm-8">
        <span class="h4 mb-0">Chester-le-Street ASC</span>
        <h1 class="mb-0">Medical Form</h1>
      </div>
      <div class="col-sm-4 d-none d-sm-block text-right">
        <button type="submit" class="btn btn-success">
          Save
        </button>
        <a class="btn btn-dark" href="<? echo autoUrl("swimmers/" . $id); ?>">
          Return to Swimmer
        </a>
      </div>
    </div>
		<? if (isset($_SESSION['ErrorState'])) {
			echo $_SESSION['ErrorState'];
			unset($_SESSION['ErrorState']);
		} ?>
    <div class="my-3 p-3 bg-white rounded box-shadow">
  		<p class="lead">Check the details for <? echo $name; ?> are correct.</p>

  		<div class="mb-2">
  			<p class="mb-2">Does <? echo $name; ?> have any specific medical conditions
  			or disabilities?</p>

  			<? if ($row['Conditions'] != "") {
  				$yes = " checked ";
  				$no = "";
  			} else {
  				$yes = "";
  				$no = " checked ";
  			} ?>

  			<div class="custom-control custom-radio">
  			  <input type="radio" value="0" <? echo $no; ?> id="medConDisNo" name="medConDis" class="custom-control-input">
  			  <label class="custom-control-label" for="medConDisNo">No</label>
  			</div>
  			<div class="custom-control custom-radio">
  			  <input type="radio" value="1" <? echo $yes; ?> id="medConDisYes" name="medConDis" class="custom-control-input">
  			  <label class="custom-control-label" for="medConDisYes">Yes</label>
  			</div>
  		</div>

  		<div class="form-group">
  	    <label for="medConDisDetails">If yes give details</label>
  	    <textarea class="form-control" id="medConDisDetails" name="medConDisDetails"
  	    rows="3"><? echo $row['Conditions']; ?></textarea>
  	  </div>

  		<!-- -->

  		<div class="mb-2">
  			<p class="mb-2">Does <? echo $name; ?> have any allergies?</p>

  			<? if ($row['Allergies'] != "") {
  				$yes = " checked ";
  				$no = "";
  			} else {
  				$yes = "";
  				$no = " checked ";
  			} ?>

  			<div class="custom-control custom-radio">
  			  <input type="radio" value="0" <? echo $no; ?> id="allergiesNo"
  			  name="allergies" class="custom-control-input">
  			  <label class="custom-control-label" for="allergiesNo">No</label>
  			</div>
  			<div class="custom-control custom-radio">
  			  <input type="radio" value="1" <? echo $yes; ?> id="allergiesYes"
  			  name="allergies" class="custom-control-input">
  			  <label class="custom-control-label" for="allergiesYes">Yes</label>
  			</div>
  		</div>

  		<div class="form-group">
  	    <label for="allergiesDetails">If yes give details</label>
  	    <textarea class="form-control" id="allergiesDetails" name="allergiesDetails"
  	    rows="3"><? echo $row['Allergies']; ?></textarea>
  	  </div>

  		<!-- -->

  		<div class="mb-2">
  			<p class="mb-2">Does <? echo $name; ?> take any regular medication?</p>

  			<? if ($row['Medication'] != "") {
  				$yes = " checked ";
  				$no = "";
  			} else {
  				$yes = "";
  				$no = " checked ";
  			} ?>

  			<div class="custom-control custom-radio">
  			  <input type="radio" value="0" <? echo $no; ?> id="medicineNo" name="medicine" class="custom-control-input">
  			  <label class="custom-control-label" for="medicineNo">No</label>
  			</div>
  			<div class="custom-control custom-radio">
  			  <input type="radio" value="1" <? echo $yes; ?> id="medicineYes" name="medicine" class="custom-control-input">
  			  <label class="custom-control-label" for="medicineYes">Yes</label>
  			</div>
  		</div>

  		<div class="form-group">
  	    <label for="medConDisDetails">If yes give details</label>
  	    <textarea class="form-control" id="medicineDetails" name="medicineDetails"
  	    rows="3"><? echo $row['Medication']; ?></textarea>
  	  </div>

  		<p class="mb-0">
  			<button type="submit" class="btn btn-success">Save</button>
  		</p>
    </div>
	</form>
</div>

<?php include BASE_PATH . "views/footer.php";
