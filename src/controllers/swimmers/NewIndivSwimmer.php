<?php

$fluidContainer = true;

global $db;

$markdown = new ParsedownExtra();
$markdown->setSafeMode(true);

$check = null;
if ($_SESSION['AccessLevel'] == 'Parent') {
  $query = $db->prepare("SELECT COUNT(*) FROM members WHERE MemberID = ? AND UserID = ?");
  $query->execute([$id, $_SESSION['UserID']]);
  $check = $query->fetchColumn();
} else {
  $query = $db->prepare("SELECT COUNT(*) FROM members WHERE MemberID = ?");
  $query->execute([$id]);
  $check = $query->fetchColumn();
}

if ($check < 1) {
  halt(404);
}

$getSwimmer = $db->prepare("SELECT members.UserID, members.MForename, members.MForename, members.MMiddleNames,
members.MSurname, members.ASANumber, members.ASACategory, members.ClubPays,
squads.SquadName, squads.SquadFee, squads.SquadCoach, squads.SquadTimetable,
squads.SquadCoC, members.DateOfBirth, members.Gender, members.OtherNotes,
members.AccessKey, memberPhotography.Website, memberPhotography.Social,
memberPhotography.Noticeboard, memberPhotography.FilmTraining,
memberPhotography.ProPhoto, memberMedical.Conditions, memberMedical.Allergies,
memberMedical.Medication FROM (((members INNER JOIN squads ON members.SquadID =
squads.SquadID) LEFT JOIN `memberPhotography` ON members.MemberID =
memberPhotography.MemberID) LEFT JOIN `memberMedical` ON members.MemberID =
memberMedical.MemberID) WHERE members.MemberID = ?");
$getSwimmer->execute([$id]);
$s = $getSwimmer->fetch(PDO::FETCH_ASSOC);

$parent = $s['UserID'];
$age = date_diff(date_create($s['DateOfBirth']),
date_create('today'))->y;


$pagetitle = htmlspecialchars($s['MForename'] . ' ' . $s['MSurname']) . ' - Swimmer';

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <h1><?=htmlspecialchars($s['MForename'] . ' ' . $s['MSurname'])?></h1>
  <p class="lead">
    <?=htmlspecialchars($s['SquadName'])?> Squad Swimmer
  </p>

  <h2>Basic Information</h2>

  <div class="row">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <h3 class="h5">
        Date of Birth
      </h3>
      <p>
        <?=date("j F Y", strtotime($s['DateOfBirth']))?>
      </p>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <h3 class="h5">
        Age
      </h3>
      <p>
        <?=htmlspecialchars($s['MForename'])?> is <?=$age?> years old
      </p>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <h3 class="h5">
        Swim England Number
      </h3>
      <p>
        <a href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=<?=htmlspecialchars($s["ASANumber"])?>" target="_blank" title="ASA Biographical Data">
          <span class="mono"><?=htmlspecialchars($s["ASANumber"])?></span> <i class="fa fa-external-link" aria-hidden="true"></i>
        </a>
      </p>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <h3 class="h5">
        Swim England Membership Category
      </h3>
      <p>
        Category <?=htmlspecialchars($s["ASACategory"])?>
      </p>
    </div>
  </div>

  <h2>Medical Information</h2>

    <div class="row">
      <div class="col-md-4">
        <h3>
          Medical Conditions or Disabilities
        </h3>
        <?php if ($s["Conditions"] != "") { ?>
          <?=$markdown->text($s["Conditions"])?>
        <?php } else { ?>
          <p>None</p>
        <?php } ?>
      </div>

      <div class="col-md-4">
        <h3>
          Allergies
        </h3>
        <?php if ($s["Allergies"] != "") { ?>
          <?=$markdown->text($s["Allergies"])?>
        <?php } else { ?>
          <p>None</p>
        <?php } ?>
      </div>

      <div class="col-md-4">
        <h3>
        Medication
        </h3>
        <?php if ($s["Medication"] != "") { ?>
          <?=$markdown->text($s["Medication"])?>
        <?php } else { ?>
          <p>None</p>
        <?php } ?>
      </div>
    </div>

</div>

<?php

include BASE_PATH . 'views/footer.php';