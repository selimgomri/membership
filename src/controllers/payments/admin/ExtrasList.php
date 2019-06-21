<?php

global $db;

$user = $_SESSION['UserId'];
$pagetitle = "Extras";

$extras = $db->query("SELECT * FROM `extras` ORDER BY `ExtraName` ASC");
$row = $extras->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
  <div class="">
  	<h1>Extras</h1>
    <p class="lead">Extras include CrossFit - Fees paid in addition to Squad Fees</p>
    <p>All extras are billed on a monthly basis</p>
    <?php if ($row != null) { ?>
      <div class="table-responsive-md">
        <table class="table">
          <thead class="thead-light">
            <tr>
              <th>Extra</th>
              <th>Cost</th>
            </tr>
          </thead>
          <tbody>
          <?php do { ?>
            <tr>
              <td>
                <a href="<?=autoUrl("payments/extrafees/" . htmlspecialchars($row['ExtraID']))?>">
                  <?=htmlspecialchars($row['ExtraName'])?>
                </a>
              </td>
              <td>&pound;<?=htmlspecialchars($row['ExtraFee'])?></td>
            </tr>
          <?php } while ($row = $extras->fetch(PDO::FETCH_ASSOC)); ?>
        </tbody>
      </table>
    </div>
    <?php } else { ?>
    <div class="alert alert-info">
      <strong>There are no extras available</strong>
    </div>
    <?php } ?>
    <p class="mb-0">
      <a href="<?=autoUrl("payments/extrafees/new")?>"
        class="btn btn-dark">
        Add New Extra
      </a>
    </p>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
