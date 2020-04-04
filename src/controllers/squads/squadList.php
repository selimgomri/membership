<?php


global $db;
$squads = $db->query("SELECT SquadID, SquadName, SquadFee, SquadCoach FROM squads ORDER BY SquadFee DESC, SquadName ASC");

$getCoaches = $db->prepare("SELECT Forename fn, Surname sn FROM coaches INNER JOIN users ON coaches.User = users.UserID WHERE coaches.Squad = ? ORDER BY coaches.Type ASC, Forename ASC, Surname ASC");

$access = $_SESSION['AccessLevel'];
$pagetitle = "Squads";
include BASE_PATH . "views/header.php";

?>

<div class="front-page mb-n3">
	<div class="container">
		<h1>Squad Details</h1>
		<p class="lead">Information about our squads</p>
    <p>
      For full details about squads, please visit out website.
    </p>

		<?php if (isset($_SESSION['DeleteSuccess']) && $_SESSION['DeleteSuccess']) { ?>
		<div class="alert alert-success">We've deleted that squad. That action cannot be undone.</div>
		<?php unset($_SESSION['DeleteSuccess']); } ?>

    <div class="mb-4">
      <div class="news-grid">

      <?php
      while ($row = $squads->fetch(PDO::FETCH_ASSOC)) {
				$getCoaches->execute([$row['SquadID']]);
				$coaches = $getCoaches->fetchAll(PDO::FETCH_ASSOC);
      ?>
        <a href="<?=htmlspecialchars(autoUrl("squads/" . $row['SquadID']))?>">
  				<span class="mb-3">
            <span class="title mb-0">
  						<?=htmlspecialchars($row['SquadName'])?> Squad
  					</span>
  					<span>
  						<?php for ($i=0; $i < min(sizeof($coaches), 3); $i++) { ?>
								<?=htmlspecialchars($coaches[$i]['fn'] . ' ' . $coaches[$i]['sn'])?><?php if ($i < min(sizeof($coaches), 3)-1) { ?>, <?php } ?>
							<?php } ?>
							<?php if (sizeof($coaches) > 3) { ?> <em>and more</em><?php } ?>
							<?php if (sizeof($coaches) == 0) { ?>
								No assigned coaches
							<?php } ?>
  					</span>
  				</span>
          <span class="category">
  					&pound;<?=number_format($row['SquadFee'], 2)?> per month
  				</span>
        </a>
      <?php
      }
      ?>

      </div>
    </div>

		<?php if ($access == "Admin") { ?>
		<p>
			<a href="<?=autoUrl("squads/new")?>" class="btn btn-success">Add a Squad <span class="fa fa-chevron-right"></span></a>
		</p>
		<?php } ?>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
