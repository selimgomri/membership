<?php

$db = app()->db;

$venues = $db->query("SELECT VenueID, VenueName, Location FROM sessionsVenues ORDER BY VenueName ASC");

$pagetitle = "Venues";
include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Venues</h1>
      <p class="lead">
        Venues used for sessions at <?=htmlspecialchars(app()->tenant->getKey('CLUB_NAME'))?>
      </p>
      <p>
        You need to populate the Membership System with venues before you can
        add sessions for squad registers.
      </p>
      <p>
        <a href="<?=autoUrl("attendance/venues/new")?>" class="btn btn-primary">
          New Venue
        </a>
      </p>


      <?php
      while ($row = $venues->fetch(PDO::FETCH_ASSOC)) {
        $address = explode(',', $row['Location'])?>
        <div class="cell">
          <h2><?=htmlspecialchars($row['VenueName'])?></h2>
          <ul class="list-unstyled">
            <?php for ($i = 0; $i < sizeof($address); $i++) {
              $strong = $strong_end = "";
              if ($i == 0) {
                $strong = "<strong>";
                $strong_end = "</strong>";
              } ?>
              <li><?=$strong?><?=htmlspecialchars(trim($address[$i]))?><?=$strong_end?></li>
            <?php } ?>
          </ul>
          <p class="mb-0">
            <a href="<?=autoUrl("attendance/venues/" . $row['VenueID'])?>" class="btn btn-primary">
              Edit
            </a>
        </div>

        <?php } ?>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
