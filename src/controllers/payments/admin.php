<?php

$user = $_SESSION['UserId'];
$pagetitle = "Payments Administration";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

$dateString = date("F Y");

 ?>

<div class="container">
	<h1>Payment Administration</h1>
	<p class="lead">Control Direct Debit Payments</p>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3 p-3 bg-white rounded box-shadow">
      	<h2>Manual Billing Information</h2>
      	<p>
          <a href="<? echo autoUrl("payments/fees"); ?>">
            Parent Fees
          </a>
        </p>
        <h2>Create a Manual Charge</h2>
      	<p>
          <a href="<? echo autoUrl("payments/newcharge"); ?>">
            New Charge
          </a>
        </p>
        <h2>Charge for Gala Entries</h2>
      	<p>
          <a href="<? echo autoUrl("payments/galas"); ?>">
            Charge for Gala Entries
          </a>
        </p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="mb-3 p-3 bg-white rounded box-shadow">
      	<h2>Payment Status</h2>
      	<p class="mb-0"><a href="<? echo autoUrl("payments/history/status/" .
      	date("Y/m") . "/squads"); ?>">Squad Fees for <? echo $dateString;
      	?></a></p>
      	<p><a href="<? echo autoUrl("payments/history/status/" . date("Y/m") .
      	"/extras"); ?>">Extra Fees for <? echo $dateString; ?></a></p>
      </div>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
