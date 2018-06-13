<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payments and Direct Debits";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

//$customers = $client->customers()->list()->records;
//print_r($customers);

$sql = "SELECT * FROM `payments` WHERE `UserID` = '$user' ORDER BY `PaymentID` DESC LIMIT 0, 5;";
$paymentResult = mysqli_query($link, $sql);

$sql = "SELECT * FROM `paymentsPending` WHERE `UserID` = '$user' AND `PMkey` IS NULL AND `Status` = 'Pending' ORDER BY `Date` DESC LIMIT 0, 30;";
$pendingResult = mysqli_query($link, $sql);

 ?>

<div class="container">
  <div class="row align-items-center">
    <div class="col-md-6 col-lg-8">
    	<h1>Payments</h1>
    	<p class="lead">Here you can control your Direct Debit details and see your payment history</p>
    </div>
    <div class="col text-center">
      <div class="p-3 text-white bg-primary rounded box-shadow">
        <p class="mb-0">Your Payment Date is the <strong><? echo getBillingDate($link, $user); ?></strong> of each month</p>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
    	<h2>Billing Account Options</h2>
    	<a href="<? echo autoUrl("payments/setup"); ?>" class="btn btn-dark">Add Bank Account</a>
    	<a href="<? echo autoUrl("payments/mandates"); ?>" class="btn btn-dark">Switch Bank Account</a>
    </div>
    <div class="col-md-6">
    	<h2>Transaction History (Bank)</h2>
      <? echo paymentHistory($link, $user); ?>
      <h2>Fees this period</h2>
      <p class="lead">Fees to pay on your next Billing Date</p>
    	<? echo feesToPay($link, $user); ?>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";
