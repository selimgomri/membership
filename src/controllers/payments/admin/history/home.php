<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payment History";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

$date = date("Y/m");

 ?>

<div class="container">
	<h1 class="border-bottom border-gray pb-2 mb-2">Payment History &amp; Status</h1>
  <div class="row">
    <div class="col-md-6 col-lg-4">
      <div class="mb-3 p-3 bg-white rounded shadow">
        <h2 class="border-bottom border-gray pb-2 mb-2">View Direct Debit charges by Month</h2>
        <p class="lead">Select a year and month to view the status of Direct Debit Payments.</p>
        <ul>
          <? for ($i = 0; $i < 12; $i++) {
          $targetDate = strtotime($now . " - " . $i . " months"); ?>
          <li>
            <a href="<? echo autoUrl("payments/history/" . date("Y/m", $targetDate)); ?>">
              <? echo date("F Y", $targetDate); ?>
            </a>
          </li>
          <? } ?>
        </ul>
        <h4 class="border-bottom border-gray pb-2 mb-2">View by Parent</h4>
        <p class="lead mb-0">
          <a href="<? echo autoUrl("payments/history/users"); ?>">
            Search for a Parent
          </a>
        </p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="mb-3 p-3 bg-white rounded shadow">
        <h2 class="border-bottom border-gray pb-2 mb-2">Squad Fee Payment Status</h2>
        <p class="lead">Select to view Squad Fee Status by Month</p>
        <ul>
          <? for ($i = 0; $i < 12; $i++) {
          $targetDate = strtotime($now . " - " . $i . " months"); ?>
          <li>
            <a href="<? echo autoUrl("payments/history/squads/" . date("Y/m", $targetDate)); ?>">
              <? echo date("F Y", $targetDate); ?>
            </a>
          </li>
          <? } ?>
        </ul>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="mb-3 p-3 bg-white rounded shadow">
        <h2 class="border-bottom border-gray pb-2 mb-2">Extra Fee Payment Status</h2>
        <p class="lead">Select to view Extra Fee Status by Month</p>
        <ul>
          <? for ($i = 0; $i < 12; $i++) {
          $targetDate = strtotime($now . " - " . $i . " months"); ?>
          <li>
            <a href="<? echo autoUrl("payments/history/extras/" . date("Y/m", $targetDate)); ?>">
              <? echo date("F Y", $targetDate); ?>
            </a>
          </li>
          <? } ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
