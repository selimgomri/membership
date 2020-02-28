<?php

$user = $_SESSION['UserID'];
$userInfo = null;
$otherUser = false;

global $db;

if ($_SESSION['AccessLevel'] != "Parent" && $person != null) {
  $user = $person;
  $otherUser = true;
}

$userInfo = $db->prepare("SELECT Forename, Surname FROM users WHERE UserID = ?");
$userInfo->execute([$user]);

$userInfo = $userInfo->fetch(PDO::FETCH_ASSOC);

if ($userInfo == null) {
  halt(404);
}

$qualifications = $db->prepare("SELECT COUNT(*) FROM qualifications WHERE UserID = ?");
$qualifications->execute([$user]);

$count = $qualifications->fetchColumn();

if ($count > 0) {
  $qualifications = $db->prepare("SELECT `Name`, Info, `From`, `To` FROM qualifications INNER JOIN qualificationsAvailable ON qualifications.Qualification = qualificationsAvailable.ID WHERE UserID = ? ORDER BY `Name` ASC");
  $qualifications->execute([$user]);
}

$pagetitle = "My Qualifications";
if ($otherUser) {
  $pagetitle = $userInfo['Forename'] . " " . $userInfo['Surname'] . "'s Qualifications";
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1><?=htmlspecialchars($pagetitle)?></h1>

  <div class="row">
    <div class="col-md-8">
      <p class="lead">
        Below are <?php if ($otherUser) { ?>their<?php } else { ?>your<?php } ?>
        qualifications the club is aware of.
      </p>
      <p>
        We use this data to remind  <?php if ($otherUser) { ?>them<?php } else {
        ?>you<?php } ?> when to renew <?php if ($otherUser) { ?>their<?php } else {
        ?>your<?php } ?> qualifications and for our own
        administrative purposes.
      </p>

      <?php if ($count == 0) { ?>
      <div class="alert alert-warning">
        <p class="mb-0"><strong><?php if ($otherUser) { ?>They<?php } else {
        ?>You<?php } ?> don't have any qualifications to list.</strong></p>

        <p class="mb-0">
          If this is a mistake, please contact the secretary to have <?php if
          ($otherUser) { ?>their<?php } else { ?>your<?php } ?> qualifications
          added to the system.
        </p>
      </div>
      <?php } else {
      while ($qualification = $qualifications->fetch(PDO::FETCH_ASSOC)) { ?>
      <div class="cell">
      <h2><?=htmlspecialchars($qualification['Name'])?></h2>
        <p><?=htmlspecialchars($qualification['Info'])?></p>
        <p>
          Valid since <?=date("d/m/Y", strtotime($qualification['From']))?><?php if ($qualification['To'] != null) { ?>, <strong>Expires <?=date("d/m/Y", strtotime($qualification['To']))?></strong><?php } ?>.
        </p>
      </div>
      <?php }
      } ?>

      <?php if ($_SESSION['AccessLevel'] == "Admin") { ?>
      <p>
        <a class="btn btn-success" href="<?=currentUrl()?>new">
          Add Qualification <span class="fa fa-chevron-right"></span>
        </a>
      </p>
      <?php } ?>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
