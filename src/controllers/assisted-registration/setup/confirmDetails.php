<?php

$_SESSION['AssRegStage'] = 2;

global $db;

$getUser = $db->prepare("SELECT UserID, Forename, Surname, EmailAddress, Mobile, `Password` FROM users WHERE UserID = ?");
$getUser->execute([$_SESSION['AssRegGuestUser']]);
$user = $getUser->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

$email = "";
$sms = "";

if (isset($_SESSION['AssRegGetDetailsError']) && $_SESSION['AssRegGetDetailsError']) {
  if ($_SESSION['AssRegGetDetailsPostData']['emailAuthorise']) {
    $email = " checked ";
  }
  if ($_SESSION['AssRegGetDetailsPostData']['smsAuthorise']) {
    $sms = " checked ";
  }
} 

$pagetitle = "Your Details - Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Your details</h1>
      <p class="lead">
        We need a few more details from you
      </p>

      <?php if (isset($_SESSION['AssRegGetDetailsError']) && $_SESSION['AssRegGetDetailsError']) { ?>
      <div class="alert alert-danger">
        <p><strong>There was a problem</strong></p>
        <?=$_SESSION['AssRegGetDetailsMessage']?>
      </div>
      <?php } ?>

      <p>
        We have to ask you some of these questions so that we comply with the General Data Protection Regulation.
      </p>

      <form method="post">
        <h2>
          Create a password for your account
        </h2>

        <p class="mb-0">
          The email address you will login with is:
        </p>

        <p class="text-truncate">
          <?=htmlspecialchars($user['EmailAddress'])?>
        </p>

        <div class="form-row">
          <div class="col-sm">
            <div class="form-group">
              <label for="password-1">Create a password</label>
              <input type="password" class="form-control" id="password-1" name="password-1" autocomplete="new-password" required aria-describedby="pwHelp">
              <small id="pwHelp" class="form-text text-muted">
                Use 8 characters or more
              </small>
            </div>
          </div>

          <div class="col-sm">
            <div class="form-group">
              <label for="password-2">Confirm your password</label>
              <input type="password" class="form-control" id="password-2" name="password-2" autocomplete="new-password" required>
            </div>
          </div>
        </div>

        <h2>
          Communication Preferences
        </h2>

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
            In accordance with European Law, <?=htmlspecialchars(env('CLUB_NAME'))?>, Swim England and British Swimming are Data Controllers for the purposes of the General Data Protection Regulation.
          </p>

          <p>
            By proceeding you agree to our <a href="https://www.chesterlestreetasc.co.uk/policies/privacy/" target="_blank">Privacy Policy</a> and the use of your data by <?=htmlspecialchars(env('CLUB_NAME'))?>. Please note that you have also agreed to our use of you and your swimmer's data as part of your registration with the club and with British Swimming and Swim England (Formerly known as the ASA).
          </p>

          <p>
            We will be unable to provide this service (and your club membership) for technical reasons if you do not consent to the use of this data.
          </p>

          <?php if (bool(env('IS_CLS'))) { ?>
          <p class="mb-0">
            Contact a member of your committee if you have any questions or email <a href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>.
          </p>
          <?php } else { ?>
          <p class="mb-0">
            Contact a member of your committee if you have any questions.
          </p>
          <?php } ?>
        </div>

        <p>
          <button class="btn btn-success" type="submit">
            Continue
          </button>
        </p>
      </form>

    </div>
  </div>
</div>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

if (isset($_SESSION['AssRegGetDetailsMessage'])) {
  unset($_SESSION['AssRegGetDetailsMessage']);
}
if (isset($_SESSION['AssRegGetDetailsError'])) {
  unset($_SESSION['AssRegGetDetailsError']);
}
if (isset($_SESSION['AssRegGetDetailsPostData'])) {
  unset($_SESSION['AssRegGetDetailsPostData']);
}

include BASE_PATH . 'views/footer.php';