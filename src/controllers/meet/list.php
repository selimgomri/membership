<?php

global $db;

$threeHoursAgo = new DateTime('-3 hours', new DateTimeZone('UTC'));

$meets = $db->prepare("SELECT ID, `Name`, `StartTime`, Forename, Surname, `Started` FROM meets INNER JOIN users ON meets.Creator = users.UserID WHERE StartTime >= ? AND NOT Finished ORDER BY StartTime ASC");
$meets->execute([
  $threeHoursAgo->format("Y-m-d H:i:s")
]);
$meet = $meets->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Join a call";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>Join a call</h1>
  <p class="lead">Join video calls hosted by club staff.</p>

  <div class="row">
    <div class="col-lg-8">
      <div class="alert alert-info">
        <p class="mb-0">
          <strong>This is currently a temporary service launched in response to the coronavirus (COVID-19) pandemic.</strong>
        </p>
        <p class="mb-0">
          The service is available so that clubs can run real-time land training sessions, or other activities as they see fit. We will review the quality of and how this service has been used after the pandemic is over and normal training has resumed.
        </p>
      </div>

      <?php if ($_SESSION['AccessLevel'] != 'Parent') { ?>
      <h2>Create a call</h2>
      <p class="lead">
        Plan a video call.
      </p>

      <p>
        You can schedule a call at any time in the future. Other users will be able to access a link, but cannot join the call until you have joined it.
      </p>

      <p>
        <a href="<?=htmlspecialchars(autoUrl("meet/new"))?>" class="btn btn-success">New call</a>
      </p>
      <?php } ?>

      <h2>Choose a call</h2>

      <?php if ($meet == null) { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>There are no meets to join at this time.</strong>
          </p>
          <p class="mb-0">
            Try again later.
          </p>
        </div>
      <?php } else  { ?>
        <div class="list-group">
          <?php do {
            $date = new DateTime($meet['StartTime'], new DateTimeZone('UTC')); 
            $date->setTimezone(new DateTimeZone('Europe/London')); ?>
          <a href="<?=htmlspecialchars(autoUrl("meet/" . $meet['ID']))?>" class="list-group-item list-group-item-action">
            <h2><?=htmlspecialchars($meet['Name'])?></h2>
            <div class="row justify-content-between">
              <div class="col-auto">
                <?=htmlspecialchars($meet['Forename'] . ' ' . $meet['Surname'])?>
              </div>
              <div class="col-auto">
                <?=htmlspecialchars($date->format("H:i, l J F Y"))?>
              </div>
            </div>
          </a>
          <?php } while ($meet = $meets->fetch(PDO::FETCH_ASSOC)); ?>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render(); ?>
