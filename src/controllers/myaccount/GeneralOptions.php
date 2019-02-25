<?php

$use_white_background = true;

global $db;

$twofaChecked;
if (filter_var(getUserOption($_SESSION['UserID'], "Is2FA"), FILTER_VALIDATE_BOOLEAN)) {
	$twofaChecked = " checked ";
}

$pagetitle = "General Account Options";
include BASE_PATH . "views/header.php";
  $userID = $_SESSION['UserID'];
?>
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Manage General Account Options</h1>
      <p class="lead">Options such as Two Factor Authentication.</p>

    	<? if ($_SESSION['OptionsUpdate']) { ?>
    		<div class="alert alert-success">
    			<p class="mb-0">
    				<strong>We've successfully updated your general options</strong>
    			</p>
    		</div>
    	<? unset($_SESSION['OptionsUpdate']);
    	} ?>

    	<form method="post">
        <div class="cell">
          <h2>
            Account Security
          </h2>
          <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
      		<div class="form-group">
      			<div class="custom-control custom-switch">
      				<input type="checkbox" class="custom-control-input" value="1" id="2FA" aria-describedby="2FAHelp" name="2FA" <?=$twofaChecked?> >
              <label class="custom-control-label" for="2FA">Enable Two Factor Authentication</label>
      				<small id="2FAHelp" class="form-text text-muted">2FA provides an increased level of security for your club account</small>
      			</div>
      		</div>
          <?php } ?>

          <?php if (filter_var(getUserOption($_SESSION['UserID'], "Is2FA"), FILTER_VALIDATE_BOOLEAN) || $_SESSION['AccessLevel'] != "Parent") { ?>

          <p>
            You can use an Authenticator App such as Google Authenticator to get Two
            Factor Authentication codes if you wish. You can always still get codes
            by email if you don't have your device on you.
          </p>

          <?php if (!filter_var(getUserOption($_SESSION['UserID'], "hasGoogleAuth2FA"), FILTER_VALIDATE_BOOLEAN)) { ?>
          <p>
            <a href="<?=autoUrl("myaccount/googleauthenticator")?>" class="btn btn-primary">
              Use an Authenticator App
            </a>
          </p>
          <?php } else { ?>
          <p>
            You can disable your Authenticator App and go back to getting codes by
            email here.
          </p>

          <p>
            <a href="<?=autoUrl("myaccount/googleauthenticator")?>" class="btn btn-primary">
              Manage Authenticator App
            </a>
          </p>

          <p>
            <a href="<?=autoUrl("myaccount/googleauthenticator/disable")?>" class="btn btn-dark">
              Disable Authenticator App
            </a>
          </p>
          <?php } ?>

          <?php } ?>
        </div>

        <div class="cell">
          <h2>
            Your account, your data
            <br><small>Export a copy</small>
          </h2>
          <p>
            Under the General Data Protection Regulation, you can request for free
            to download all personal data held about you by <?=CLUB_NAME?>.
          </p>
          <p>
            <a href="<?=autoUrl("myaccount/general/download-personal-data")?>"
            class="btn btn-primary">
              Download your data
            </a>
          </p>
          <p>
            You can download the personal data for your swimmers from their
            respective information pages.
          </p>
        </div>

    		<p class="mb-0">
    			<button type="submit" class="btn btn-secondary">Update Details</button>
    		</p>
    	</form>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>