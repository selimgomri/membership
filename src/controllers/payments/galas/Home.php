<?php

$user = $_SESSION['UserId'];
$pagetitle = "Gala Payments";

$sql = "SELECT * FROM `galas` WHERE `ClosingDate` <= CURDATE() AND `GalaDate` >= CURDATE();";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

 ?>

<div class="container">
  <div class="my-3 p-3 bg-white rounded shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Payments for Galas</h1>
  	<p class="lead">Charge Parents for Galas</p>
  	<div class="alert alert-info">
  		<strong>When using Direct Debit, we charge parents after recieving Accepted Entries</strong> <br>
  		This means that there is no need to handle refunds.
  	</div>
    <hr>
    <? if ($result > 0) { ?>
      <h2>Galas to Charge For</h2>
      <ul class="list-unstyled mb-0">
        <? for ($i = 0; $i < $count; $i++) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
          <li><a href="<? echo autoUrl("payments/galas/" . $row['GalaID']); ?>"><? echo $row['GalaName']; ?></a></li>
        <? } ?>
      </ul>
    <? } else { ?>
      <div class="alert alert-info mb-0">
        <strong>There are no galas open for charges</strong>
      </div>
    <? } ?>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
