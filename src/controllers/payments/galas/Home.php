<?php

global $db;

$user = $_SESSION['UserID'];
$pagetitle = "Gala Payments";

$galas = $db->query("SELECT * FROM `galas` WHERE `GalaDate` >= CURDATE() AND `GalaDate` >= CURDATE()");
$gala = $galas->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

 ?>

<div class="container">
  <nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
			<li class="breadcrumb-item active" aria-current="page">Galas</li>
		</ol>
	</nav>

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
        <h2>Galas to Charge or Refund</h2>
        <ul class="list-group">
          <?php do { ?>
          <li class="list-group-item list-group-item-action">
            <p class="mb-0">
              <strong>
                <a href="<?=autoUrl("galas/" . $gala['GalaID']); ?>">
                  <?=htmlspecialchars($gala['GalaName'])?>
                </a>
              </strong>
            </p>
            <p class="mb-0">
              <a href="<?=autoUrl("galas/" . $gala['GalaID'] . '/charges'); ?>">Charge for Entries</a> or <a href="<?=autoUrl("galas/" . $gala['GalaID'] . '/refunds'); ?>">Issue Refunds</a>
            </p>
          </li>
          <?php } while ($gala = $galas->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>
      <?php } else { ?>
        <div class="alert alert-warning">
          <strong>There are no galas open for charges</strong>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
