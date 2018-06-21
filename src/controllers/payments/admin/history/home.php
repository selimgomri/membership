<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payment History";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

$date = date("Y/m");

 ?>

<div class="container">
	<h1>Payment History</h1>
  <p class="lead">Select a year and month to view the status of Direct Debit Payments.</p>
  <ul>
    <? for ($i = 0; $i < 12; $i++) {
    $targetDate = strtotime($now . " - " . $i . " months"); ?>
    <li><a href="<? echo autoUrl("payments/history/" . date("Y/m", $targetDate)); ?>"><? echo date("F Y", $targetDate); ?></a>
    <? } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
