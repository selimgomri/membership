<?php

$pagetitle = "Privacy Policy";

$db = app()->db;
$systemInfo = app()->system;
$privacy = $systemInfo->getSystemOption('PrivacyPolicy');

$Extra = new ParsedownExtra();
$Extra->setSafeMode(true);
$search  = array("\n##### ", "\n#### ", "\n### ", "\n## ", "\n# ");
$replace = array("\n###### ", "\n######", "\n#### ", "\n### ", "\n## ");

$privacyPolicy = null;
if ($privacy != null && $privacy != "") {
  $privacyPolicy = $db->prepare("SELECT Content FROM posts WHERE ID = ?");
  $privacyPolicy->execute([$privacy]);
  $privacyPolicy = str_replace($search, $replace, $privacyPolicy->fetchColumn());
  if ($privacyPolicy[0] == '#') {
    $privacyPolicy = '#' . $privacyPolicy;
  }
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Privacy Policy</h1>
      <?php if ($privacyPolicy == null) { ?>
        <p class="lead">
          Your club has no privacy policy set.
        </p>
        <p>
          Please speak to a member of club staff urgently about this issue.
        </p>
        <p>
          Clubs are not permitted by SCDS to use this software without a privacy policy in place.
        </p>
        <hr>
        <p>
          In accordance with European Law, <?=htmlspecialchars(env('CLUB_NAME'))?>, Swim England and British Swimming are Data Controllers for the purposes of the General Data Protection Regulation.
        </p>
        <p>
          By proceeding you agree to our <a href="https://www.chesterlestreetasc.co.uk/policies/privacy/" target="_blank">Privacy Policy (this is an example policy)</a> and the use of your data by <?=htmlspecialchars(env('CLUB_NAME'))?>. Please note that you have also agreed to our use of you and your swimmer's data as part of your registration with the club and with British Swimming and Swim England (Formerly known as the ASA).
        </p>
        <p>
          We will be unable to provide this service for technical reasons if you do not consent to the use of this data.
        </p>
        <p class="mb-0">
          Contact a member of your committee if you have any questions or email <a href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>.
        </p>
      <?php } else { ?>
        <?=$Extra->text($privacyPolicy)?>
      <?php } ?>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();