<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberType;
use Brick\PhoneNumber\PhoneNumberFormat;

$tenant = app()->tenant;

$_SESSION['TENANT-' . app()->tenant->getId()]['AssRegStage'] = 2;

$db = app()->db;

$privacy = app()->tenant->getKey('PrivacyPolicy');

$Extra = new ParsedownExtra();
$Extra->setSafeMode(true);
$search  = array("\n##### ", "\n#### ", "\n### ", "\n## ", "\n# ");
$replace = array("\n###### ", "\n###### ", "\n##### ", "\n#### ", "\n### ");

$privacyPolicy = null;
if ($privacy != null && $privacy != "") {
  $privacyPolicy = $db->prepare("SELECT Content FROM posts WHERE ID = ?");
  $privacyPolicy->execute([$privacy]);
  $privacyPolicy = str_replace($search, $replace, $privacyPolicy->fetchColumn());
  if ($privacyPolicy[0] == '#') {
    $privacyPolicy = '##' . $privacyPolicy;
  }
}

$getUser = $db->prepare("SELECT UserID, Forename, Surname, EmailAddress, Mobile, `Password` FROM users WHERE UserID = ?");
$getUser->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser']]);
$user = $getUser->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

$members = $db->prepare("SELECT MemberID, MForename, MSurname, ASANumber FROM members WHERE UserID = ? ORDER BY MForename ASC, MSurname ASC;");
$members->execute([
  $user['UserID'],
]);

$email = "";
$sms = "";

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError']) {
  if ($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsPostData']['emailAuthorise']) {
    $email = " checked ";
  }
  if ($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsPostData']['smsAuthorise']) {
    $sms = " checked ";
  }
}

$examplePhone = '+447400123456';
try {
  $examplePhone = PhoneNumber::getExampleNumber('GB', PhoneNumberType::MOBILE)->format(PhoneNumberFormat::E164);
} catch (Exception $e) {
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

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsError']) { ?>
        <div class="alert alert-danger">
          <p><strong>There was a problem</strong></p>
          <?= $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGetDetailsMessage'] ?>
        </div>
      <?php } ?>

      <p>
        We have to ask you some of these questions so that we comply with the General Data Protection Regulation.
      </p>

      <form method="post" class="needs-validation" novalidate>
        <?php if (mb_strtoupper($tenant->getKey('ASA_CLUB_CODE')) == 'UOSZ' || mb_strlen($user['Mobile']) == 0) { ?>
          <h2>About You</h2>

          <!-- Mobile -->
          <div class="form-group">
            <label for="mobile-number">Mobile phone number</label>
            <input type="tel" class="form-control" name="mobile-number" id="mobile-number" required placeholder="<?= htmlspecialchars($examplePhone) ?>" aria-describedby="mobile-number-help">
            <div class="invalid-feedback">
              You must provide a valid phone number (with no spaces).
            </div>
            <small class="text-muted" id="mobile-number-help">
              Please provide your mobile phone number. We may need it for coronavirus (COVID-19) contact tracing. For non UK (not starting +44) phone numbers, please include your country code.
            </small>
          </div>

          <?php if (mb_strtoupper($tenant->getKey('ASA_CLUB_CODE')) == 'UOSZ' && $member = $members->fetch(PDO::FETCH_ASSOC)) { ?>

            <!-- Ask for Swim England number -->
            <p>
              Do you have a Swim England (ASA) or other UK aquatics governing body registration number? If so we may be able to use this in future to get information from the British Rankings Database.
            </p>

            <?php do { ?>
              <div class="form-group">
                <label for="<?= htmlspecialchars('swim-england-' . $member['MemberID']) ?>"><?= htmlspecialchars($member['MForename'] . ' ' . $member['MSurname']) ?> Swim England/Swim Wales/Scottish Swimming Number</label>
                <input type="text" aria-describedby="<?= htmlspecialchars('help-swim-england-' . $member['MemberID']) ?>" name="<?= htmlspecialchars('swim-england-' . $member['MemberID']) ?>" id="<?= htmlspecialchars('swim-england-' . $member['MemberID']) ?>" class="form-control">
                <small class="text-muted" id="<?= htmlspecialchars('help-swim-england-' . $member['MemberID']) ?>">You can skip this field if you want to</small>
              </div>
            <?php } while ($member = $members->fetch(PDO::FETCH_ASSOC)); ?>

          <?php } ?>
        <?php } ?>

        <h2>
          Create a password for your account
        </h2>

        <p class="mb-0">
          The email address you will login with is:
        </p>

        <p class="text-truncate">
          <?= htmlspecialchars($user['EmailAddress']) ?>
        </p>

        <div class="form-row" id="password-form-row">
          <div class="col-sm">
            <div class="form-group">
              <label for="password-1">Create a password</label>
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
            <div class="form-group">
              <label for="password-2">Confirm your password</label>
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
            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="emailAuthorise" id="emailAuthorise" value="1" <?= $email ?>>
                <label class="custom-control-label" for="emailAuthorise">
                  I wish to receive important email updates about my squads.
                  This includes emails about session cancellations.
                </label>
              </div>
            </div>

            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="smsAuthorise" id="smsAuthorise" value="1" <?= $sms ?>>
                <label class="custom-control-label" for="smsAuthorise">
                  I wish to receive text message notifications
                </label>
                <small class="d-block"><?= htmlspecialchars(app()->tenant->getName()) ?> may not offer this service</small>
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
