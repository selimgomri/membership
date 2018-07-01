<?php
  $pagetitle = "Register";
  $preventLoginRedirect = true;
  include BASE_PATH . "views/header.php";

?>
<div class="frontpage1" style="margin-top:-1.0rem;">
  <div class="container">
    <div class="my-3 p-3 bg-white rounded box-shadow">
      <? if (isset($_SESSION['RegistrationGoVerify'])) {
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
      <? if (isset($_SESSION['ErrorState'])) {
        echo $_SESSION['ErrorState'];
        unset($_SESSION['ErrorState']);
      } ?>
      <form method="post" action="register" name="register" id="register">

        <h2>Personal Details</h2>
        <div class="form-group">
          <label for="forename">First Name</label>
          <input class="form-control" type="text" name="forename" id="forename"
          placeholder="First Name" required value="<? echo
          $_SESSION['RegistrationForename']; ?>">
        </div>
        <div class="form-group">
          <label for="surname">Last Name</label>
          <input class="form-control" type="text" name="surname" id="surname" placeholder="Last Name" required value="<? echo $_SESSION['RegistrationSurname']; ?>">
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input class="form-control mb-0" type="email" name="email" id="email" placeholder="yourname@example.com" required value="<? echo $_SESSION['RegistrationEmail']; ?>">
          <small id="emailHelp" class="form-text text-muted">Your email address will only be used inside Chester-le-Street ASC.</small>
        </div>
        <div class="form-group">
          <label for="mobile">Mobile Number</label>
          <input class="form-control" type="tel" name="mobile" id="mobile" placeholder="01234 567890" required value="<? echo $_SESSION['RegistrationMobile']; ?>">
          <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
        </div>

        <h2>Username and Password</h2>
        <div class="form-group">
          <label for="username">Username</label>
          <input class="form-control" type="text" name="username" id="username" placeholder="Username" aria-labelledby="usernameHelp" required value="<? echo $_SESSION['RegistrationUsername']; ?>">
          <small id="usernameHelp" class="form-text text-muted">This username is for your user account as an adult, not your swimmer(s)</small>
        </div>
        <div class="form-group">
          <label for="password1">Password</label>
          <input class="form-control" type="password" name="password1" id="password1" placeholder="Password" required>
        </div>
        <div class="form-group">
          <label for="password2">Confirm Password</label>
          <input class="form-control" type="password" name="password2" id="password2" placeholder="Password" required>
        </div>

        <h2>Notification Preferences</h2>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="emailAuthorise" value="1">
          <label class="custom-control-label" for="emailAuthorise">
            I wish to recieve email updates about my squads
          </label>
        </div>

        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="smsAuthorise" value="1">
          <label class="custom-control-label" for="smsAuthorise">
            I wish to recieve text message notifications
          </label>
        </div>

        <p class="small">We will still need to send you notifications relating to your account from time.</p>

        <div class="alert alert-info">
          <p class="mb-0"><strong>Legal Stuff Applies</strong></p>
          <p>In accordance with European Law, Chester-le-Street ASC is a Data Controller for the purposes of the General Data Protection Regulation.</p>
          <p>By proceeding you agree to our <a class="alert-link" href="https://www.chesterlestreetasc.co.uk/policies/privacy/" target="_blank">Privacy Policy</a> and the use of your data by Chester-le-Street ASC. Please note that you have also agreed to our use of you and your swimmer's data as part of your registration with the club and with British Swimming and Swim England (Formerly known as the ASA).</p>
          <p>We will be unable to provide this service for technical reasons if you do not consent to the use of this data.</p>
          <p class="mb-0">Contact a member of the committee if you have any questions or email <a class="alert-link" href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>.</p>
        </div>
        <input type="submit" class="btn btn-outline-dark" value="Register">
      </form>
      <? } ?>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";

unset($_SESSION['RegistrationUsername']);
unset($_SESSION['RegistrationForename']);
unset($_SESSION['RegistrationSurname']);
unset($_SESSION['RegistrationEmail']);
unset($_SESSION['RegistrationMobile']);
