<?php

$user = $_SESSION['UserId'];
$pagetitle = "Payments Administration";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

 ?>

<div class="container">
	<h1>Payment Administration</h1>
	<p class="lead">Control Direct Debit Payments</p>
  <hr>
  <div class="row">
    <div class="col-md-6">
    	<h2>Manual Billing Information</h2>
    	<p><a href="<? echo autoUrl("payments/fees"); ?>" class="btn btn-dark">Parent Fees</a></p>
      <h2>Create a Manual Charge</h2>
    	<p><a href="<? echo autoUrl("payments/charge"); ?>" class="btn btn-dark">New Charge</a></p>
      <h2>Charge for Gala Entries</h2>
    	<p><a href="<? echo autoUrl("payments/galas"); ?>" class="btn btn-dark">Charge for Gala Entries</a></p>
    </div>
    <div class="col-md-6">
      <h2>Make Refund</h2>
    	<p><a href="<? echo autoUrl("payments/refunds"); ?>" class="btn btn-dark">Refund a Parent</a></p>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
