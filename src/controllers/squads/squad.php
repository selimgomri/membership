<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$Extra = new ParsedownExtra();
$Extra->setSafeMode(true);
$search  = array("\n##### ", "\n#### ", "\n### ", "\n## ", "\n# ");
$replace = array("\n###### ", "\n##### ", "\n#### ", "\n### ", "\n## ");
//echo $Extra->text('# Header {.sth}'); # prints: <h1 class="sth">Header</h1>

$db = app()->db;

$squad = null;
try {
  $squad = Squad::get($id);
} catch (Exception $e) {
  halt(404);
}

$numSwimmers = $db->prepare("SELECT COUNT(*) FROM members WHERE SquadID = ?");
$numSwimmers->execute([$id]);
$numSwimmers = $numSwimmers->fetchColumn();

$codeOfConduct = $squad->getCodeOfConductMarkdown();
if ($codeOfConduct) {
  $codeOfConduct = str_replace($search, $replace, $codeOfConduct);
  if ($codeOfConduct[0] == '#') {
    $codeOfConduct = '#' . $codeOfConduct;
  }
}

// See if this squad is allowed
$canAccessSquadInfo = false;
$isAllowed = $db->prepare("SELECT COUNT(*) FROM squadReps WHERE User = ? AND Squad = ?");
$isAllowed->execute([
  $_SESSION['UserID'],
  $id
]);
if ($isAllowed->fetchColumn() > 0) {
  // User cannot access this squad
  $canAccessSquadInfo = true;
}

$swimmers = null;
if ($_SESSION['AccessLevel'] != 'Parent' || $canAccessSquadInfo) {
  $swimmers = $db->prepare("SELECT MemberID id, MForename first, MSurname last, DateOfBirth dob, Forename fn, Surname sn, EmailAddress email, Mobile mob, members.UserID `user` FROM members LEFT JOIN users ON members.UserID = users.UserID WHERE SquadID = ? ORDER BY first ASC, last ASC");
  $swimmers->execute([$id]);
}

$coaches = $squad->getCoaches();

$pagetitle = htmlspecialchars($squad->getName());

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("squads")?>">Squads</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($squad->getName())?></li>
    </ol>
  </nav>
  <div class="row align-items-center mb-3">
    <div class="col-md-6">
      <h1><?=htmlspecialchars($squad->getName())?></h1>
      <p class="lead">
        This squad has <?=htmlspecialchars($numSwimmers)?> swimmers
      </p>
    </div>
    <?php if ($_SESSION['AccessLevel'] == 'Admin') { ?>
    <div class="col text-sm-right">
      <a href="<?=autoUrl("squads/" . $id . "/edit")?>"
        class="btn btn-dark">Edit squad</a>
    </div>
    <?php } ?>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <h2>About this squad</h2>
      <dl class="row">
        <dt class="col-sm-3">Monthly fee</dt>
        <dd class="col-sm-9">&pound;<?=htmlspecialchars($squad->getFee(false))?></dd>

        <dt class="col-sm-3">Squad coach<?php if (sizeof($coaches) != 1) { ?>es<?php } ?></dt>
        <dd class="col-sm-9">
          <ul class="list-unstyled mb-0">
          <?php for ($i=0; $i < sizeof($coaches); $i++) { ?>
            <li><strong><?=htmlspecialchars($coaches[$i]->getFullName())?></strong>, <?=htmlspecialchars($coaches[$i]->getType())?></li>
          <?php } ?>
          <?php if (sizeof($coaches) == 0) { ?>
            <li>None assigned</li>
          <?php } ?>
          </ul>
        </dd>

        <dt class="col-sm-3">Squad timetable</dt>
        <dd class="col-sm-9 text-truncate">
          <a href="<?=htmlspecialchars($squad->getTimetableUrl())?>" target="_blank">
            <?=htmlspecialchars(trim(str_replace(['https://www.', 'http://www.', 'http://', 'https://'], '', $squad->getTimetableUrl()), '/'))?>
          </a>
        </dd>
      </dl>

      <?php if ($_SESSION['AccessLevel'] != 'Parent') { ?>
      <?php $members = $squad->getMembers(); ?>
      <h2><?=htmlspecialchars($squad->getName())?> Members</h2>
      <div class="list-group mb-3">
      <?php foreach ($members as $member) { ?>
        <a href="<?=autoUrl("members/" . $member->getId())?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <?=htmlspecialchars($member->getFullName())?>
          <span class="badge badge-primary badge-pill rounded">Age <?=htmlspecialchars(($member->getAge()))?></span>
        </a>
      <?php } ?>
      </div>
      <?php } ?>

      <?php if ($canAccessSquadInfo) { ?>
      <?php $members = $squad->getMembers(); ?>
      <h2><?=htmlspecialchars($squad->getName())?> Members</h2>
      <ul class="list-group mb-3 accordion" id="memberContactAccordion">
      <?php foreach ($members as $member) { ?>
        <li class="list-group-item">
          <div class="d-flex justify-content-between align-items-center">
            <?=htmlspecialchars($member->getFullName())?>
            <span class="badge badge-primary badge-pill rounded">Age <?=htmlspecialchars(($member->getAge()))?></span>
          </div>
          
          <?php if ($user = $member->getUser()) { ?>
          <p class="mb-0 mt-3">
            <a data-toggle="collapse" href="#details-<?=htmlspecialchars($member->getId())?>" role="button" aria-expanded="false" aria-controls="details-<?=htmlspecialchars($member->getId())?>" class="btn btn-primary">Show contact details</a>
          </p>
          <div class="collapse" id="details-<?=htmlspecialchars($member->getId())?>" data-parent="#memberContactAccordion">
            <div class="cell mb-0 mt-3">
              <p>Contact <?=htmlspecialchars($member->getFullName())?> by email or phone.</p>
              <dl class="row mb-0">
                <dt class="col-md-3">Email</dt>
                <dd class="col-md-9"><a href="<?=htmlspecialchars("mailto:" . $user->getEmail())?>"><?=htmlspecialchars($user->getEmail())?></a></dd>

                <dt class="col-md-3">Phone</dt>
                <dd class="col-md-9 mb-0">
                <?php try { $mobile = PhoneNumber::parse((string) $user->getMobile()); ?>
                <a href="<?=htmlspecialchars($mobile->format(PhoneNumberFormat::RFC3966))?>"><?=htmlspecialchars($mobile->format(PhoneNumberFormat::NATIONAL))?></a>
                <?php } catch (PhoneNumberParseException | Exception $e) { ?>
                The user's phone number is not valid
                <?php } ?>
                </dd>
              </dl>
            </div>
          </div>
          <?php } ?>
        </li>
      <?php } ?>
      </ul>
      <?php } ?>

      <?php if ($_SESSION['AccessLevel'] != "Parent") { ?>
      <?php if ($numSwimmers > 0) { ?>
      <h2>Sex Split</h2>
      <canvas class="mb-3" id="sexSplit"></canvas>
      <?php } ?>

      <h2>Age Distribution</h2>
      <p class="lead">The age distribution chart shows the number of swimmers of each age in this squad.</p>
      <canvas class="mb-3" id="ageDistribution"></canvas>
      <?php } ?>

      <?php if ($codeOfConduct != null) { ?>
      <h2>Code of conduct for <?=htmlspecialchars($squad->getName())?> Squad</h2>

      <?=$Extra->text($codeOfConduct)?>
      <?php } ?>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
if ($_SESSION['AccessLevel'] != "Parent") {
  $footer->addJs("public/js/Chart.min.js");
  $footer->addJs("js/charts/squad.js?squad=" . $id);
}
$footer->render();
