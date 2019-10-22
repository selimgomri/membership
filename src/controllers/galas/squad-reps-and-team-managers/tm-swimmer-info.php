<?php

// Verify user has access
canView('TeamManager', $_SESSION['UserID'], $id);

global $db;

// GET THE GALA
$getGala = $db->prepare("SELECT GalaName `name`, GalaVenue venue FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
  halt(404);
}

// GET SWIMMER INFO
$getSwimmers = $db->prepare("SELECT MForename fn, MSurname sn, SquadName squad, Website, Social, Noticeboard, FilmTraining, ProPhoto, Conditions, Allergies, Medication FROM ((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) LEFT JOIN memberMedical ON galaEntries.MemberID = memberMedical.MemberID) LEFT JOIN memberPhotography ON galaEntries.MemberID = memberPhotography.MemberID) INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE galaEntries.GalaID = ?");
$getSwimmers->execute([$id]);
$swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($gala['name']) . " Swimmer Information";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
		<ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas/" . $id)?>">This Gala</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("galas/" . $id . "/team-manager")?>">TM Dashboard</a></li>
			<li class="breadcrumb-item active" aria-current="page">Swimmers</li>
		</ol>
	</nav>

  <div class="row">
    <div class="col-md-8">

      <h1><?=htmlspecialchars($gala['name'])?> swimmer information</h1>
      <p class="lead">View swimmer medical information and emergency contact details</p>

      <?php if ($swimmer == null) { ?>

      <?php } else { ?>

      <ul class="list-group">
        <?php $i = 0; do { ?>
        <li class="list-group-item <?php if ($i%2 == 1) { ?>bg-light<?php } ?>">
          <h2><?=htmlspecialchars($swimmer['fn'] . " " . $swimmer['sn'])?></h2>
          <p class="lead">
            <?=htmlspecialchars($swimmer['squad'])?> Squad
          </p>

          <div class="row">
            <div class="col-sm">
              <h3>Medical information</h3>
              <h4>Medical conditions</h4>
              <h4>Allergies</h4>
              <h4>Medication</h4>
            </div>
            <div class="col-sm">
              <h3>Emergency contacts</h3>
            </div>
          </div>

        </li>
        <?php $i++; } while ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>

      <?php } ?>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';