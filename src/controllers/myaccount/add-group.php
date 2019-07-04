<?php

$use_white_background = true;
$pagetitle = "Add Several Swimmers";
include BASE_PATH . "views/header.php";

$errorMessage = "";
$errorState = false;
?>

<div class="container">
  <div class="">
    <h1>Add a Family Group</h1>
		<?php if (!$_SESSION['Success']) { ?>
    <p>If you have a Family Signup Sheet, you can add a group of your swimmers in one go.</p>
    <?php if (isset($_SESSION['ErrorState'])) {
      ?>
			<div class="alert alert-danger">
        <p class="mb-0"><strong>We were unable to find a group of swimmers
        matching those details.</strong></p>
        <p class="mb-0">Please check them and try again.</p>
      </div>
			<?php
			$fam = $_SESSION['ErrorState']['FAM'];
			$acs = $_SESSION['ErrorState']['ACS'];
      unset($_SESSION['ErrorState']);
    } ?>
    <hr>
    <form method="post" action="<?php echo autoUrl("my-account/addswimmergroup"); ?>" name="register" id="register">
      <h2>Details</h2>
      <div class="form-group">
        <label for="fam">Family Registration Number</label>
        <input class="form-control mb-0" type="text" name="fam" id="fam"
        placeholder="FAM1" required value="<?php echo htmlentities($fam); ?>"
        style="text-transform:uppercase;"
        keyup="javascript:this.value=this.value.toUpperCase();">
      </div>
      <div class="form-group">
        <label for="sec">Security Key</label>
        <input class="form-control mb-0" type="text" name="sec" id="sec"
        placeholder="1A3B5C" required value="<?php echo htmlentities($acs); ?>">
      </div>

      <input type="submit" class="btn btn-success" value="Add Swimmers">
    </form>
	<?php } else { ?>
		<div class="alert alert-success">
			<p class="mb-0">
				<strong>We have successfully added the swimmers from your family
				group.</strong>
			</p>
			<p class="mb-0">
				If you need to complete the registration process for these swimmers, you
				will see a link to follow below. Otherwise, return to <a href="<?php echo
				autoUrl("swimmers"); ?>" class="alert-link">My Swimmers</a>.
			</p>
	<?php } ?>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
