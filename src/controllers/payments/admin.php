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
    	<a href="<? echo autoUrl("payments/fees"); ?>" class="btn btn-dark">Parent Fees</a>
    </div>
    <div class="col-md-6">
    	<h2>Stuff to go here</h2>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
