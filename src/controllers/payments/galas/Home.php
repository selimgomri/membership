<?php

global $db;

$user = $_SESSION['UserId'];
$pagetitle = "Gala Payments";

$galas = $db->query("SELECT * FROM `galas` WHERE `ClosingDate` <= CURDATE() AND `GalaDate` >= CURDATE()");
$gala = $galas->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

 ?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1 class="">Payments for Galas</h1>
      <p class="lead">Charge Parents for Galas</p>
      <div class="cell">
        <strong>When using Direct Debit, we charge parents after submitting entries</strong> <br>
        Rejections are handled as soon as they are returned.
      </div>
      <hr>
      <?php if ($gala != null) { ?>
        <h2>Galas to Charge For</h2>
        <div class="list-group">
          <?php do { ?>
          <a class="list-group-item list-group-item-action" href="<?=autoUrl("payments/galas/" . $gala['GalaID']); ?>">
            <?=htmlspecialchars($gala['GalaName'])?>
          </a>
          <?php } while ($gala = $galas->fetch(PDO::FETCH_ASSOC)); ?>
        </div>
      <?php } else { ?>
        <div class="alert alert-warning">
          <strong>There are no galas open for charges</strong>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
