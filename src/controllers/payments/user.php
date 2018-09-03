<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payments and Direct Debits";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

 ?>

<div class="container">
  <div class="row align-items-center">
    <div class="col-md-6 col-lg-8">
    	<h1>Payments</h1>
    	<p class="lead">Here you can control your Direct Debit details and see your payment history</p>
    </div>
    <div class="col text-center">
      <div class="p-3 text-white bg-primary rounded shadow">
        <p class="mb-0">Your Payment Date is the <strong><? echo getBillingDate($link, $user); ?></strong> of each month</p>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="my-3 p-3 bg-white rounded shadow">
      	<h2 class="border-bottom border-gray pb-2 mb-2">Billing Account Options</h2>
        <? if (userHasMandates($user)) { ?>
          <p>We currently collect payments from <? echo ucwords(strtolower(bankDetails($user, "bank_name"))); ?>, Account Ending ******<? echo bankDetails($user, "account_number_end"); ?></p>
        <? } ?>
        <p class="mb-0">
        	<a href="<? echo autoUrl("payments/setup"); ?>" class="btn btn-dark btn-block">Add Bank Account</a>
          <? if (userHasMandates($user)) { ?>
        	<a href="<? echo autoUrl("payments/mandates"); ?>" class="btn btn-dark btn-block">Switch or Manage Bank Account</a>
          <? } ?>
        </p>
      </div>
      <div class="p-3 text-white bg-secondary rounded shadow">
        <p class="mb-0">
          <strong>
            We help keep things simple!
          </strong>
        </p>
        <p class="mb-0">
          That's why if you switch your current account through the Current
          Account Switch Service, we'll update your details for you, before
          you even have time to tell us.
        </p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="my-3 p-3 bg-white rounded shadow">
      	<h2 class="border-bottom border-gray pb-2 mb-0">Transaction History (Bank)</h2>
        <? echo paymentHistory($link, $user); ?>
      </div>
      <div class="my-3 p-3 bg-white rounded shadow">
        <h2 class="border-bottom border-gray pb-2 mb-1">Fees this period</h2>
        <p class="lead">Fees to pay on your next Billing Date</p>
      	<? echo feesToPay($link, $user); ?>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";
