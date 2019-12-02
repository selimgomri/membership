<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

$Extra = new ParsedownExtra();
$Extra->setSafeMode(true);
$search  = array("\n##### ", "\n#### ", "\n### ", "\n## ", "\n# ");
$replace = array("\n###### ", "\n##### ", "\n#### ", "\n### ", "\n## ");
//echo $Extra->text('# Header {.sth}'); # prints: <h1 class="sth">Header</h1>

global $db;
$getSquad = $db->prepare("SELECT SquadName, SquadFee, SquadCoC, SquadTimetable, SquadCoach FROM squads WHERE SquadID = ?");
$getSquad->execute([$id]);
$squad = $getSquad->fetch(PDO::FETCH_ASSOC);

$numSwimmers = $db->prepare("SELECT COUNT(*) FROM members WHERE SquadID = ?");
$numSwimmers->execute([$id]);
$numSwimmers = $numSwimmers->fetchColumn();

$codeOfConduct = null;
if ($squad['SquadCoC'] != null && $squad['SquadCoC'] != "") {
  $codeOfConduct = $db->prepare("SELECT Content FROM posts WHERE ID = ?");
  $codeOfConduct->execute([$squad['SquadCoC']]);
  $codeOfConduct = str_replace($search, $replace, $codeOfConduct->fetchColumn());
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

$pagetitle = htmlspecialchars($squad['SquadName']) . ' Squad';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("squads")?>">Squads</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($squad['SquadName'])?></li>
    </ol>
  </nav>
  <div class="row align-items-center mb-3">
    <div class="col-md-6">
      <h1><?=htmlspecialchars($squad['SquadName'])?> Squad</h1>
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
        <dd class="col-sm-9">&pound;<?=htmlspecialchars(number_format($squad['SquadFee'],2))?></dd>

        <?php if ($squad['SquadCoach'] != null && $squad['SquadCoach'] != "") { ?>
        <dt class="col-sm-3">Squad coach(s)</dt>
        <dd class="col-sm-9"><?=htmlspecialchars($squad['SquadCoach'])?></dd>
        <?php } ?>

        <dt class="col-sm-3">Squad timetable</dt>
        <dd class="col-sm-9 text-truncate">
          <a href="<?=htmlspecialchars($squad['SquadTimetable'])?>" target="_blank">
            <?=htmlspecialchars(str_replace(['https://www.', 'http://www.'], '', $squad['SquadTimetable']))?>
          </a>
        </dd>
      </dl>

      <?php if ($_SESSION['AccessLevel'] != 'Parent') { ?>
      <h2>Swimmers in <?=htmlspecialchars($squad['SquadName'])?> Squad</h2>
      <div class="list-group mb-3">
      <?php while ($swimmer = $swimmers->fetch(PDO::FETCH_ASSOC)) { ?>
        <a href="<?=autoUrl("swimmers/" . $swimmer['id'])?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <?=htmlspecialchars($swimmer['first'] . ' ' . $swimmer['last'])?>
          <span class="badge badge-primary badge-pill rounded">Age <?=date_diff(date_create($swimmer['dob']), date_create('now'))->y?></span>
        </a>
      <?php } ?>
      </div>
      <?php } ?>

      <?php if ($canAccessSquadInfo) { ?>
      <h2>Swimmers in <?=htmlspecialchars($squad['SquadName'])?> Squad</h2>
      <ul class="list-group mb-3 accordion" id="swimmerContactAccordion">
      <?php while ($swimmer = $swimmers->fetch(PDO::FETCH_ASSOC)) { ?>
        <li class="list-group-item">
          <div class="d-flex justify-content-between align-items-center">
            <?=htmlspecialchars($swimmer['first'] . ' ' . $swimmer['last'])?>
            <span class="badge badge-primary badge-pill rounded">Age <?=date_diff(date_create($swimmer['dob']), date_create('now'))->y?></span>
          </div>
          
          <?php if ($swimmer['user'] != null) { ?>
          <p class="mb-0 mt-3">
            <a data-toggle="collapse" href="#details-<?=htmlspecialchars($swimmer['id'])?>" role="button" aria-expanded="false" aria-controls="details-<?=htmlspecialchars($swimmer['id'])?>" class="btn btn-primary">Show contact details</a>
          </p>
          <div class="collapse" id="details-<?=htmlspecialchars($swimmer['id'])?>" data-parent="#swimmerContactAccordion">
            <div class="cell mb-0 mt-3">
              <p>Contact <?=htmlspecialchars($swimmer['fn'])?> <?=htmlspecialchars($swimmer['sn'])?> by email or phone.</p>
              <dl class="mb-0">
                <dt>Email</dt>
                <dd><a href="<?=htmlspecialchars("mailto:" . $swimmer['email'])?>"><?=htmlspecialchars($swimmer['email'])?></a></dd>

                <dt>Phone</dt>
                <?php try { $mobile = PhoneNumber::parse((string) $swimmer['mob']); ?>
                <dd class="mb-0"><a href="<?=htmlspecialchars($mobile->format(PhoneNumberFormat::RFC3966))?>"><?=htmlspecialchars($mobile->format(PhoneNumberFormat::NATIONAL))?></a></dd>
                <?php } catch (PhoneNumberParseException | Exception $e) { ?>
                <dd class="mb-0">The user's phone number is not valid</dd>
                <?php } ?>
              </dl>
            </div>
            <?php } ?>
          </div>
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
      <h2>Code of conduct for <?=htmlspecialchars($squad['SquadName'])?> Squad</h2>

      <?=$Extra->text($codeOfConduct)?>
      <?php } ?>

    </div>
  </div>
</div>

<?php if ($_SESSION['AccessLevel'] != "Parent") { ?>
<script src="<?=autoUrl("public/js/Chart.min.js")?>"></script>
<script src="<?=autoUrl("js/charts/squad.js?squad=" . $id)?>"></script>
<?php } ?>

<?php

include BASE_PATH . 'views/footer.php';
