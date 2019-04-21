<?php

global $db;

$mode = "Default";
$fam_keys = null;
/*$fam_keys = [
  "FAM" => null,
  "KEY" => null
];*/

$_SESSION['RegistrationMode'] = $mode;

  $use_white_background = true;
  $pagetitle = "Register";
  $preventLoginRedirect = true;
  include BASE_PATH . "views/header.php";

?>
<div class="pb-3">
  <div class="container">
      <?php if (isset($_SESSION['RegistrationGoVerify'])) {
        echo $_SESSION['RegistrationGoVerify'];
        unset($_SESSION['RegistrationGoVerify']);
      }  else { ?>
      <!--<div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            Registration is currently closed for maintenance
          </strong>
        </p>
        <p>
          We're working to improve this service and will reopen user
          registration as soon as possible.
        </p>

        <p class="mb-0">
          If you've just recieved a letter about getting registered today, don't
          worry. If you don't need to make gala entries there's no rush yet -
          We'll be back by tomorrow. You will need to ensure you register for
          this system and connect your swimmers to your account by 1 September
          2018 ahead of the new season.
        </p>
      </div>-->
      <h1>User Registration</h1>
      <p>We need a few details before we start.</p>
      <hr>
      <?php if (isset($_SESSION['ErrorState'])) {
        echo $_SESSION['ErrorState'];
        unset($_SESSION['ErrorState']);
      } ?>
      <form method="post" action="<?php echo autoUrl("register"); ?>" name="register" id="register">

        <h2>About you</h2>
        <div class="row">
          <div class="col-md-8 col-lg-6">

            <div class="form-row">

              <div class="col">

                <div class="form-group">
                  <label for="forename">First Name</label>
                  <input class="form-control" type="text" name="forename"
                  id="forename" placeholder="First" required
                  value="<?=htmlspecialchars($_SESSION['RegistrationForename'])?>">
                </div>

              </div>

              <div class="col">

                <div class="form-group">
                  <label for="surname">Last Name</label>
                  <input class="form-control" type="text" name="surname"
                  id="surname" placeholder="Last" required
                  value="<?=htmlspecialchars($_SESSION['RegistrationSurname'])?>">
                </div>

              </div>

            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input class="form-control mb-0" type="email" name="email" id="email-address" placeholder="yourname@example.com" required value="<?=htmlspecialchars($_SESSION['RegistrationEmail'])?>">
              <small id="emailHelp" class="form-text text-muted">
                Your email address will only be used inside <?=CLUB_NAME?> and
                Chester-le-Street ASC Club Digital Services.<!-- Emails sent by
                Chester-le-Street ASC Club Digital Services are delivered by Google
                Cloud and SendGrid. Both companies are EU-US Privacy Shield
                certified.-->
              </small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8 col-lg-6">
            <div class="form-group">
              <label for="mobile">Mobile Number</label>
              <input class="form-control" type="tel" name="mobile" id="mobile" placeholder="01234 567890" required value="<?=htmlspecialchars($_SESSION['RegistrationMobile'])?>">
              <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
            </div>
          </div>
        </div>

        <!--<h2>Password</h2>-->
        <div class="row">
          <div class="col-md-8 col-lg-6">
            <div class="form-row">
              <div class="col">
                <div class="form-group">
                  <label for="password1">Password</label>
                  <input class="form-control" type="password" aria-describedby="pwHelp" name="password1" id="password1" placeholder="Password" required>
                  <small id="pwHelp" class="form-text text-muted">Use 8 characters or more</small>
                </div>
              </div>

              <div class="col">
                <div class="form-group">
                  <label for="password2">Confirm Password</label>
                  <input class="form-control" type="password" name="password2" id="password2" placeholder="Password" required>
                </div>
              </div>
            </div>
          </div>
        </div>

        <h2>Notification Preferences</h2>

        <?

        $email = $sms = "";
        if (isset($_SESSION['RegistrationEmailAuth'])) {
          $email = " checked ";
          unset($_SESSION['RegistrationEmailAuth']);
        }
        if (isset($_SESSION['RegistrationSmsAuth'])) {
          $sms = " checked ";
          unset($_SESSION['RegistrationSmsAuth']);
        } ?>

        <div class="row">
          <div class="col-md-8 col-lg-6">
            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input"
                name="emailAuthorise" id="emailAuthorise" value="1" <?=$email?>>
                <label class="custom-control-label" for="emailAuthorise">
                  I wish to recieve important email updates about my squads.
                  This includes emails about session cancellations.
                </label>
              </div>
            </div>

            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input"
                name="smsAuthorise" id="smsAuthorise" value="1" <?=$sms?>>
                <label class="custom-control-label" for="smsAuthorise">
                  I wish to recieve text message notifications
                </label>
              </div>
            </div>

            <p class="small">
              We will still need to send you notifications relating to your
              account from time to time.
            </p>

          </div>
        </div>

        <div class="cell">
          <p class="mb-0"><strong>Legal Stuff Applies</strong></p>
          <p>
            In accordance with European Law, <?=CLUB_NAME?>, Chester-le-Street
            ASC Club Digital Services, Swim England and British Swimming are
            Data Controllers for the purposes of the General Data Protection
            Regulation.
          </p>
          <p>
            By proceeding you agree to our <a
            href="https://www.chesterlestreetasc.co.uk/policies/privacy/"
            target="_blank">Privacy Policy</a> and the use of your data by
            <?=CLUB_NAME?> and Chester-le-Street ASC Club Digital Services.
            Please note that you have also agreed to our use of you and your
            swimmer's data as part of your registration with the club and with
            British Swimming and Swim England (Formerly known as the ASA).
          </p>
          <p>
            We will be unable to provide this service for technical reasons if
            you do not consent to the use of this data.
          </p>
          <p class="mb-0">
            Contact a member of your committee if you have any questions or email
            <a
            href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>.
          </p>
        </div>
        <input type="submit" class="btn btn-primary btn-lg" value="Register">
      </form>
      <?php } ?>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";

unset($_SESSION['RegistrationUsername']);
unset($_SESSION['RegistrationForename']);
unset($_SESSION['RegistrationSurname']);
unset($_SESSION['RegistrationEmail']);
unset($_SESSION['RegistrationMobile']);
