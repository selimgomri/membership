<?php

$db = app()->db;
$tenant = app()->tenant;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberType;
use Brick\PhoneNumber\PhoneNumberFormat;

$privacy = app()->tenant->getKey('PrivacyPolicy');
$privacyPolicy = null;
if ($privacy != null && $privacy != "") {
  $privacyPolicy = $db->prepare("SELECT Content FROM posts WHERE ID = ?");
  $privacyPolicy->execute([$privacy]);
  $privacyPolicy = str_replace($search, $replace, $privacyPolicy->fetchColumn());
  if ($privacyPolicy[0] == '#') {
    $privacyPolicy = '##' . $privacyPolicy;
  }
}

$examplePhone = '+447400123456';
try {
  $examplePhone = PhoneNumber::getExampleNumber('GB', PhoneNumberType::MOBILE)->format(PhoneNumberFormat::E164);
} catch (Exception $e) {
}

$now = new DateTime('now', new DateTimeZone('Europe/London'));

$pagetitle = "Register for a trial";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Register for an account</h1>
      <p class="lead">
        Because of COVID-19, we need to collect a few details from you before you trial or train with us.
      </p>

      <p>
        <strong>Please do not complete this form if you are an existing club member.</strong>
      </p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError']) { ?>
        <div class="alert alert-danger">
          <p><strong>There was a problem</strong></p>
          <?= $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsMessage'] ?>
        </div>
      <?php } ?>

      <form method="post" class="needs-validation" novalidate>
        <h2>About You</h2>

        <div class="row">
          <div class="col">
            <div class="mb-3">
              <label class="form-label" for="first">First name</label>
              <input class="form-control" type="text" id="first" name="first" placeholder="First name" required>
              <div class="invalid-feedback">
                Please provide a first name
              </div>
            </div>
          </div>

          <div class="col">
            <div class="mb-3">
              <label class="form-label" for="last">Last name</label>
              <input class="form-control" type="text" id="last" name="last" placeholder="Last name" required>
              <div class="invalid-feedback">
                Please provide a last name
              </div>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="dob">Date of birth</label>
          <input class="form-control" type="date" id="dob" name="dob" pattern="[0-9]{4}-[0-1]{1}[0-9]{1}-[0-3]{1}[0-9]{1}" placeholder="YYYY-MM-DD" required max="<?= htmlspecialchars($now->format('Y-m-d')) ?>">
          <div class="invalid-feedback">
            Please provide a date of birth
          </div>
        </div>

        <!-- Ask for Swim England number -->
        <p>
          Do you have a Swim England (ASA) or other UK aquatics governing body registration number? If so we may be able to use this in future to get information from the British Rankings Database.
        </p>
        <div class="mb-3">
          <label class="form-label" for="<?= htmlspecialchars('swim-england') ?>">Swim England/Swim Wales/Scottish Swimming Number</label>
          <input type="text" aria-describedby="<?= htmlspecialchars('help-swim-england') ?>" name="<?= htmlspecialchars('swim-england') ?>" id="<?= htmlspecialchars('swim-england') ?>" class="form-control">
          <small class="text-muted" id="<?= htmlspecialchars('help-swim-england') ?>">You can skip this field if you don't know or have a UK NGB registration number</small>
        </div>

        <p class="mb-2">
          Sex
        </p>

        <div class="mb-3">
          <div class="form-check">
            <input type="radio" id="sex-male" name="sex" class="form-check-input" value="Male">
            <label class="form-check-label" for="sex-male">Male</label>
          </div>
          <div class="form-check">
            <input type="radio" id="sex-female" name="sex" class="form-check-input" value="Female">
            <label class="form-check-label" for="sex-female">Female</label>
          </div>
        </div>

        <h2>
          Your login details
        </h2>

        <div class="mb-3">
          <label class="form-label" for="email-address">Sheffield Email Address</label>
          <input class="form-control" type="email" id="email-address" name="email-address" pattern="[a-z0-9._%+-]+@sheffield.ac.uk" placeholder="name1@sheffield.ac.uk" required>
          <div class="invalid-feedback">
            Please use a University of Sheffield email address (ending <em>sheffield.ac.uk</em>)
          </div>
        </div>

        <!-- Mobile -->
        <div class="mb-3">
          <label class="form-label" for="mobile-number">Mobile phone number</label>
          <input type="tel" class="form-control" name="mobile-number" id="mobile-number" required placeholder="<?= htmlspecialchars($examplePhone) ?>" aria-describedby="mobile-number-help">
          <div class="invalid-feedback">
            You must provide a valid phone number (with no spaces).
          </div>
          <small class="text-muted" id="mobile-number-help">
            Please provide your mobile phone number. We may need it for coronavirus (COVID-19) contact tracing. For non UK (not starting +44) phone numbers, please include your country code.
          </small>
        </div>

        <div class="row" id="password-row">
          <div class="col-sm">
            <div class="mb-3">
              <label class="form-label" for="password-1">Create a password</label>
              <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control" id="password-1" name="password-1" autocomplete="new-password" required aria-describedby="pwHelp">
              <small id="pwHelp" class="form-text text-muted">
                Use 8 characters or more, with at least one lowercase letter, at least one uppercase letter and at least one number
              </small>
              <div class="invalid-feedback">
                You must provide password that is at least 8 characters long with at least one lowercase letter, at least one uppercase letter and at least one number
              </div>
            </div>
          </div>

          <div class="col-sm">
            <div class="mb-3">
              <label class="form-label" for="password-2">Confirm your password</label>
              <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control" id="password-2" name="password-2" autocomplete="new-password" required aria-describedby="pwConfirmHelp">
              <small id="pwConfirmHelp" class="form-text text-muted">Repeat your password</small>
              <div class="invalid-feedback" id="password-2-invalid-feedback">
                Passwords do not match
              </div>
            </div>
          </div>
        </div>

        <div class="alert alert-info d-none" id="pwned-password-warning">
          <p class="mb-0">
            <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Warning</strong>
          </p>
          <p class="mb-0">
            That password has been part of a data breach elsewhere on the internet. We suggest you pick something different.
          </p>
        </div>

        <h2>
          Communication Preferences
        </h2>

        <div class="row">
          <div class="col-md-8 col-lg-6">
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="emailAuthorise" id="emailAuthorise" value="1" <?= $email ?>>
                <label class="form-check-label" for="emailAuthorise">
                  I wish to receive important email updates about my squads.
                  This includes emails about session cancellations.
                </label>
              </div>
            </div>

            <!-- <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="smsAuthorise" id="smsAuthorise" value="1" <?= $sms ?>>
                <label class="form-check-label" for="smsAuthorise">
                  I wish to receive text message notifications
                </label>
                <small class="d-block"><?= htmlspecialchars(app()->tenant->getName()) ?> may not offer this service</small>
              </div>
            </div> -->

            <p class="small">
              We will still need to send you notifications relating to your
              account from time to time.
            </p>

          </div>
        </div>

        <div class="cell">
          <p class="mb-0"><strong>Legal Stuff Applies</strong></p>
          <?php if ($privacyPolicy != null) { ?>
            <?= $Extra->text($privacyPolicy) ?>
          <?php } else { ?>
            <p>
              YOUR CLUB HAS NOT SET UP A PRIVACY POLICY. PLEASE DO NOT PROCEED.
            </p>
            <p>
              In accordance with European Law, <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>, Swim England and British Swimming are Data Controllers for the purposes of the General Data Protection Regulation.
            </p>
            <p>
              By proceeding you agree to our <a href="https://www.chesterlestreetasc.co.uk/policies/privacy/" target="_blank">Privacy Policy (this is an example policy)</a> and the use of your data by <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>. Please note that you have also agreed to our use of you and/or your swimmer's data as part of your registration with the club and with British Swimming and Swim England.
            </p>
            <p>
              We will be unable to provide this service for technical reasons if
              you do not consent to the use of this data.
            </p>
            <p class="mb-0">
              Contact a member of your committee if you have any questions or email <?php if (app()->tenant->isCLS()) { ?><a href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a><?php } else { ?><a href="mailto:<?= htmlspecialchars(app()->tenant->getKey('CLUB_EMAIL')) ?>"><?= htmlspecialchars(app()->tenant->getKey('CLUB_EMAIL')) ?></a><?php } ?>.
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

<div id="ajax-options" data-get-pwned-list-ajax-url="<?= htmlspecialchars(autoUrl('ajax-utilities/pwned-password-check')) ?>" data-cross-site-request-forgery-value="<?= htmlspecialchars(\SCDS\CSRF::getValue()) ?>"></div>

<?php

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsMessage'])) {
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsMessage']);
}
if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError'])) {
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError']);
}
if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsPostData'])) {
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsPostData']);
}

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->addJs("public/js/ajax-utilities/pwned-password-check.js");
$footer->render();
