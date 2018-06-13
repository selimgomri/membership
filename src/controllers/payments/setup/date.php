<?php

$user = $_SESSION['UserID'];
$sql = "SELECT * FROM `paymentSchedule` WHERE `UserID` = '$user';";
$scheduleExists = mysqli_num_rows(mysqli_query($link, $sql));
if ($scheduleExists > 0) {
	header("Location: " . autoUrl("payments/setup/2"));
}

$pagetitle = "Set up a Direct Debit";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";
 ?>

<div class="container">
	<h1>Setup a Direct Debit to pay Chester-le-Street ASC</h1>
	<form method="post">
		<p>On which day of the month should Chester-le-Street ASC make Direct Debit requests?</p>
		<div class="form-group">
			<label for="chosenDay">Select Day</label>
			<select name="chosenDay" id="chosenDay" class="custom-select">
				<?php for ($i = 1; $i < 29; $i++) {?>
			  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</div>
		<button class="btn btn-dark" type="submit">Next</button>
		<p><span class="small">We'll now direct you to our partner GoCardless who handle Direct Debits on our behalf.</span></p>
	</form>
</div>

<?php include BASE_PATH . "views/footer.php";
