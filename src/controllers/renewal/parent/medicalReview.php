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

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded shadow">
		<form method="post" name="med" id="med">
			<h1>Medical Form</h1>
			<?php if (isset($_SESSION['ErrorState'])) {
				echo $_SESSION['ErrorState'];
				unset($_SESSION['ErrorState']);
			} ?>
			<p class="lead">Check the details for <?php echo $name; ?> are correct.</p>

			<div class="mb-2">
				<p class="mb-2">Does <?php echo $name; ?> have any specific medical conditions
				or disabilities?</p>

				<?php if ($row['Conditions'] != "") {
					$yes = " checked ";
					$no = "";
				} else {
					$yes = "";
					$no = " checked ";
				} ?>

				<div class="custom-control custom-radio">
				  <input type="radio" value="0" <?php echo $no; ?> id="medConDisNo" name="medConDis" class="custom-control-input" onclick="toggleState('medConDisDetails', 'medConDis')">
				  <label class="custom-control-label" for="medConDisNo">No</label>
				</div>
				<div class="custom-control custom-radio">
				  <input type="radio" value="1" <?php echo $yes; ?> id="medConDisYes" name="medConDis" class="custom-control-input" onclick="toggleState('medConDisDetails', 'medConDis')">
				  <label class="custom-control-label" for="medConDisYes">Yes</label>
				</div>
			</div>

			<div class="form-group">
		    <label for="medConDisDetails">If yes give details</label>
		    <textarea class="form-control auto-grow" id="medConDisDetails" name="medConDisDetails"
		    rows="3" oninput="autoGrow(this)" <?if($yes==""){?>disabled<?} ?>><?php echo $row['Conditions']; ?></textarea>
		  </div>

			<!-- -->

			<div class="mb-2">
				<p class="mb-2">Does <?php echo $name; ?> have any allergies?</p>

				<?php if ($row['Allergies'] != "") {
					$yes = " checked ";
					$no = "";
				} else {
					$yes = "";
					$no = " checked ";
				} ?>

				<div class="custom-control custom-radio">
				  <input type="radio" value="0" <?php echo $no; ?> id="allergiesNo"
				  name="allergies" class="custom-control-input" onclick="toggleState('allergiesDetails', 'allergies')">
				  <label class="custom-control-label" for="allergiesNo">No</label>
				</div>
				<div class="custom-control custom-radio">
				  <input type="radio" value="1" <?php echo $yes; ?> id="allergiesYes"
				  name="allergies" class="custom-control-input" onclick="toggleState('allergiesDetails', 'allergies')">
				  <label class="custom-control-label" for="allergiesYes">Yes</label>
				</div>
			</div>

			<div class="form-group">
		    <label for="allergiesDetails">If yes give details</label>
		    <textarea class="form-control auto-grow" id="allergiesDetails" name="allergiesDetails"
		    rows="3" oninput="autoGrow(this)" <?if($yes==""){?>disabled<?} ?>><?php echo $row['Allergies']; ?></textarea>
		  </div>

			<!-- -->

			<div class="mb-2">
				<p class="mb-2">Does <?php echo $name; ?> take any regular medication?</p>

				<?php if ($row['Medication'] != "") {
					$yes = " checked ";
					$no = "";
				} else {
					$yes = "";
					$no = " checked ";
				} ?>

				<div class="custom-control custom-radio">
				  <input type="radio" value="0" <?php echo $no; ?> id="medicineNo" name="medicine" class="custom-control-input" onclick="toggleState('medicineDetails', 'medicine')">
				  <label class="custom-control-label" for="medicineNo">No</label>
				</div>
				<div class="custom-control custom-radio">
				  <input type="radio" value="1" <?php echo $yes; ?> id="medicineYes" name="medicine" class="custom-control-input" onclick="toggleState('medicineDetails', 'medicine')">
				  <label class="custom-control-label" for="medicineYes">Yes</label>
				</div>
			</div>

			<div class="form-group">
		    <label for="medConDisDetails">If yes give details</label>
		    <textarea class="form-control auto-grow" id="medicineDetails" name="medicineDetails"
		    rows="3" oninput="autoGrow(this)" <?if($yes==""){?>disabled<?} ?>><?php echo $row['Medication']; ?></textarea>
		  </div>

			<div>
				<button type="submit" class="btn btn-success">Save and Continue</a>
			</div>
		</form>
	</div>
</div>

<script src="<?=autoUrl("public/js/medical-forms/MedicalForm.js")?>"></script>

<?php include BASE_PATH . "views/footer.php";
