<?php

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

$swimmers = null;
if ($_SESSION['AccessLevel'] != 'Parent') {
  $swimmers = $db->prepare("SELECT MemberID id, MForename first, MSurname last, DateOfBirth dob FROM members WHERE SquadID = ? ORDER BY first ASC, last ASC");
  $swimmers->execute([$id]);
}

$pagetitle = htmlspecialchars($squad['SquadName']) . ' Squad';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
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

      <?php if ($codeOfConduct != null) { ?>
      <h2>Code of conduct for <?=htmlspecialchars($squad['SquadName'])?> Squad</h2>

      <?=$Extra->text($codeOfConduct)?>
      <?php } ?>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';
