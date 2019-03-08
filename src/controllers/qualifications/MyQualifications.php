<?php

global $db;
$qualifications = $db->prepare("SELECT COUNT(*) FROM qualifications WHERE UserID = ?");
$qualifications->execute([$_SESSION['UserID']]);

$count = $qualifications->fetchColumn();

if ($count > 0) {
  $qualifications = $db->prepare("SELECT `Name`, Info, `From`, `To` FROM qualifications WHERE UserID = ?");
  $qualifications->execute([$_SESSION['UserID']]);
}

$pagetitle = "My Qualifications";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>My Qualifications</h1>

  <div class="row">
    <div class="col-md-8">
      <p class="lead">
        Below are your qualifications the club is aware of.
      </p>
      <p>
        We use this data to remind you when to renew your qualifications and for
        our own administrative purposes.
      </p>

      <?php if ($count == 0) { ?>
      <div class="alert alert-warning">
        <p class="mb-0"><strong>You don't have any qualifications to list.</strong></p>

        <p class="mb-0">
          If this is a mistake, please contact the secretary to have your qualifications added to the system.
        </p>
      </div>
      <?php } else { 
      while ($qualification = $qualifications->fetch(PDO::FETCH_ASSOC)) { ?>
      <div class="cell">
      <h2><?=htmlspecialchars($qualification['Name'])?></h2>
        <p><?=htmlspecialchars($qualification['Info'])?></p>
        <p>
          Valid since <?=date("d/m/Y", strtotime($qualification['From']))?>, <strong>Expires <?=date("d/m/Y", strtotime($qualification['To']))?></strong>.
        </p>
      </div>
      <?php }
      } ?>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';
