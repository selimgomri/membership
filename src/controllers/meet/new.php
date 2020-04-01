<?php

$pagetitle = "New call";

$date = new DateTime('now', new DateTimeZone('Europe/London'));

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>New call</h1>
  <p class="lead">Plan a new video call.</p>

  <div class="row">
    <div class="col-lg-8">
      <form method="post">

        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="eg A Squad (Remote) Land Training">
        </div>

        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="date">Date</label>
              <input type="date" class="form-control" id="date" name="date" value="<?=htmlspecialchars($date->format("Y-m-d"))?>">
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <label for="time">Time</label>
              <input type="time" class="form-control" id="time" name="time" value="<?=htmlspecialchars($date->format("H:i"))?>">
            </div>
          </div>
        </div>

        <p>
          <button type="submit" class="btn btn-success">
            Schedule call
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render(); ?>
