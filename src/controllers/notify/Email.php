<?php
$pagetitle = "Notify Composer";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

$sql = "SELECT `SquadName`, `SquadID` FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC;";
$result = mysqli_query($link, $sql);

 ?>

<div class="container">
	<h1>Notify</h1>
	<p class="lead">Send Emails to targeted groups of parents</p>
  <hr>
	<form method="post">
		<div class="form-group">
			<label>To parents of...</label>
			<div class="row">
			<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
				<div class="col col-sm-6 col-md-4 col-lg-3">
					<div class="custom-control custom-checkbox">
					  <input type="checkbox" class="custom-control-input" id="<? echo $row['SquadID']; ?>" name="<? echo $row['SquadID']; ?>">
					  <label class="custom-control-label" for="<? echo $row['SquadID']; ?>"><? echo $row['SquadName']; ?> Squad</label>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>

		<div class="form-group">
			<label for="subject">Message Subject</label>
			<input type="text" class="form-control" name="subject" placeholder="Message Subject" autocomplete="off">
		</div>

		<hr>

		<h2>Your Message</h2>

		<div class="form-group">
			<label for="message">Enter your message</label>
			<textarea class="form-control" id="message" name="message" rows="10"></textarea>
			<small id="messageHelp" class="form-text text-muted">Styling will be stripped from this message</small>
		</div>

		<div class="form-group mb-0">
		<button class="btn btn-success" id="submit" value="submitted" type="submit">Send the email</button>
		</div>
	</form>
</div>

<script>
 tinymce.init({
   selector: '#message',
   branding: false,
 });
</script>
<?php include BASE_PATH . "views/footer.php";
