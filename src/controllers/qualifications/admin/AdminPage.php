<?php

$db = app()->db;
$qualifications = $db->query("SELECT COUNT(*) FROM qualificationsAvailable");
$qualificationsCount = $qualifications->fetchColumn();
$qualifications = $db->query("SELECT ID, `Name` FROM qualificationsAvailable ORDER BY `Name` ASC");

$pagetitle = "Qualification Administration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>Qualification Administration</h1>

  <div class="row">
    <div class="col-md-8">

      <?php

      if ($_SESSION['AddedNewOption']) {
        unset($_SESSION['AddedNewOption']);
        ?>

        <div class="alert alert-success">
          <strong>Added the new Qualification</strong>
        </div>

      <?php } ?>

      <p class="lead">
        Welcome to the Qualifications Administration Service.
      </p>

      <p>
        This system tracks qualifications such as DBS checks, coaching
        qualifications and more.
      </p>

      <h2>
        Available Qualifications
      </h2>

      <?php if ($qualificationsCount > 0) { ?>
      <p class="lead">
        There are <?=$qualificationsCount?> qualifications available.
      </p>
      <p>
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#qualifications" aria-expanded="false" aria-controls="qualifications">
          View qualifications
        </button>
      </p>
      <div class="collapse" id="qualifications">
        <div class="cell">
          <ul class="list-unstyled mb-0">
          <?php while ($qualification = $qualifications->fetch(PDO::FETCH_ASSOC)) { ?>
            <li><a href="<?=autoUrl("admin/qualifications/" . $qualification['ID'])?>"><?=htmlspecialchars($qualification['Name'])?></a></li>
          <?php } ?>
          </ul>
        </div>
      </div>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>
              There are no available qualifications in the system.
            </strong>
          </p>

          <p class="mb-0">
            You must create a qualification before you can add qualifications to
            users.
          </p>
        </div>
      <?php } ?>

      <p>
        <a href="<?=autoUrl("admin/qualifications/new")?>" class="btn btn-success">
          Add a Qualification <span class="fa fa-chevron-right"></span>
        </a>
      </p>

      <p>
        Enter a user's email address to add a qualification.
      </p>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
