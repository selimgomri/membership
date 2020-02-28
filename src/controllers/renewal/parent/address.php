<?php

global $db;
global $currentUser;

$addr = [];
$json = $currentUser->getUserOption('MAIN_ADDRESS');
if ($json != null) {
  $addr = json_decode($json);
}

$pagetitle = "My Address";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
$userID = $_SESSION['UserID'];
?>
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>My Address</h1>
      <p class="lead">Manage your address</p>

    	<?php if (isset($_SESSION['ErrorState']) && $_SESSION['ErrorState']) { ?>
    		<div class="alert alert-danger">
    			<p class="mb-0">
    				<strong>An error occurred when we tries to save the changes</strong>
    			</p>
    		</div>
    	<?php unset($_SESSION['ErrorState']);
    	} ?>

      <?php if (isset($addr->streetAndNumber)) { ?>
      <h2>Your address is</h2>
      <address>
        <?=htmlspecialchars($addr->streetAndNumber)?><br>
        <?php if (isset($addr->flatOrBuilding)) { ?>
        <?=htmlspecialchars($addr->flatOrBuilding)?><br>
        <?php } ?>
        <?=htmlspecialchars(mb_strtoupper($addr->city))?><br>
        <?=htmlspecialchars(mb_strtoupper($addr->postCode))?><br>
      </address>

      <h2>Edit address</h2>
      <?php } else { ?>
      <h2>Add address</h2>
      <?php } ?>

      <p>You must use a UK address or a British Forces address.</p>

    	<form method="post">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="street-and-number">Address line 1 (street and number)</label>
              <input class="form-control" name="street-and-number" id="street-and-number" type="text"autocomplete="address-line1"  <?php if (isset($addr->streetAndNumber)) { ?>value="<?=htmlspecialchars($addr->streetAndNumber)?>"<?php } ?> required>
            </div>

            <div class="form-group">
              <label for="flat-building">Address line 2 (optional)</label>
              <input class="form-control" name="flat-building" id="flat-building" type="text"autocomplete="address-line2"  <?php if (isset($addr->flatOrBuilding)) { ?>value="<?=htmlspecialchars($addr->flatOrBuilding)?>"<?php } ?>>
            </div>

            <div class="form-group">
              <label for="town-city">Town/City</label>
              <input class="form-control" name="town-city" id="town-city" type="text" autocomplete="address-level2" <?php if (isset($addr->city)) { ?>value="<?=htmlspecialchars($addr->city)?>"<?php } ?> required>
            </div>

            <div class="form-group">
              <label for="county-province">County</label>
              <input class="form-control" name="county-province" id="county-province" type="text" autocomplete="address-level1" <?php if (isset($addr->county)) { ?>value="<?=htmlspecialchars($addr->county)?>"<?php } ?> required>
            </div>

            <div class="form-group">
              <label for="post-code">Post Code</label>
              <input class="form-control" name="post-code" id="post-code" type="text" autocomplete="postal-code" <?php if (isset($addr->postCode)) { ?>value="<?=htmlspecialchars($addr->postCode)?>"<?php } ?> required>
            </div>
          </div>
        </div>

    		<p class="mb-0">
    			<button type="submit" class="btn btn-success">Save and Continue</button>
    		</p>
    	</form>
    </div>
  </div>
</div>

<?php $footer = new \SDCS\Footer();
$footer->render(); ?>
