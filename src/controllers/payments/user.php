<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payments and Direct Debits";

if (!userHasMandates($user)) {
  header("Location: " . autoUrl("payments/setup"));
}

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

 ?>

<div class="bg-warning box-shadow mb-3 py-2" style="margin-top:-1rem;">
 <div class="<?=$container_class?>">
   <nav class="nav nav-underline">
     <strong>
       Remember to cancel your Standing Order for Monthly Fees
     </strong>
   </nav>
 </div>
</div>

<div class="container">
  <div class="row align-items-center">
    <div class="col-md-6 col-lg-8">
    	<h1>Payments</h1>
    	<p class="lead">Manage your Direct Debit</p>
    </div>
    <div class="col text-center">
      <div class="p-3 text-white bg-primary rounded shadow">
        <? if (userHasMandates($user)) { ?>
          <p class="mb-0">We currently collect payments from <?=strtoupper(
          bankDetails($user, "bank_name"))?>, Account Ending <span class="mono">
          ******<?=bankDetails($user, "account_number_end")?></span></p>
        <? } ?>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-8">
      <div class="my-3 p-3 bg-white rounded shadow">
      	<h2 class="border-bottom border-gray pb-2 mb-0">Billing History</h2>
        <? echo paymentHistory($link, $user); ?>
      </div>
      <div class="my-3 p-3 bg-white rounded shadow">
        <h2 class="border-bottom border-gray pb-2 mb-1">
          Extra Fees this month
        </h2>
        <p class="lead">Fees to pay on your next Billing Date, in addition to Squad Fees</p>
      	<? echo feesToPay($link, $user); ?>
      </div>
    </div>
    <div class="col">
      <div class="my-3 p-3 bg-white rounded shadow">
      	<h2 class="border-bottom border-gray pb-2 mb-2">
          My Bank Account
        </h2>
        <? if (userHasMandates($user)) {
          $name = strtoupper(bankDetails($user, "account_holder_name"));
          if ($name != "UNKNOWN") {
            $name = $name . ', ';
          } else {
            $name = null;
          }
          ?>
          <p class="mb-0"><?=$name?><?=strtoupper(bankDetails($user, "bank_name"))?></p>
          <p class="mono">******<?=strtoupper(bankDetails($user, "account_number_end"))?></p>
          <p><?=CLUB_NAME?> does not store your bank details.</p>
        <? } ?>
        <p class="mb-0">
        	<a href="<?=autoUrl("payments/setup")?>" class="btn btn-dark btn-block">Add Bank Account</a>
          <? if (userHasMandates($user)) { ?>
        	<a href="<?=autoUrl("payments/mandates")?>" class="btn btn-dark btn-block">Switch or Manage Bank Account</a>
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
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";
