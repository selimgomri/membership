<?php

$user = $_SESSION['UserId'];
$pagetitle = "Extras";

$sql = "SELECT * FROM `extras` ORDER BY `ExtraName` ASC;";
$result = mysqli_query($link, $sql);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
  <div class="">
  	<h1 class="border-bottom border-gray pb-2 mb-2">Extras</h1>
    <p class="lead">Extras include CrossFit - Fees paid in addition to Squad Fees</p>
    <p>All extras are billed on a monthly basis</p>
    <?php if (mysqli_num_rows($result) > 0) { ?>
      <div class="table-responsive-md">
        <table class="table">
          <thead class="thead-light">
            <tr>
              <th>Extra</th>
              <th>Cost</th>
            </tr>
          </thead>
          <tbody>
          <?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
            <tr>
              <td><a href="<?php echo autoUrl("payments/extrafees/" . $row['ExtraID']); ?>"><?php echo $row['ExtraName']; ?></a></td>
              <td>&pound;<?php echo $row['ExtraFee']; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <?php } else { ?>
    <div class="alert alert-info">
      <strong>There are no extras available</strong>
    </div>
    <?php } ?>
    <p class="mb-0">
      <a href="<?php echo autoUrl("payments/extrafees/new"); ?>"
        class="btn btn-dark">
        Add New Extra
      </a>
    </p>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
