<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payments and Direct Debits";

if (!userHasMandates($user)) {
  header("Location: " . autoUrl("payments/setup"));
}

$use_white_background = true;
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

?>

<!--
<div class="bg-warning box-shadow mb-3 py-2" style="margin-top:-1rem;">
 <div class="<?=$container_class?>">
   <nav class="nav nav-underline">
     <strong>
       Remember to cancel your Standing Order for Monthly Fees
     </strong>
   </nav>
 </div>
</div>
-->

<div class="container">
  <div class="row align-items-center">
    <div class="col-md-6 col-lg-8">
    	<h1>Payments</h1>
    	<p class="lead mb-0">Manage your Direct Debit Payments</p>
    </div>

  </div>
  <hr>
  <div class="row">
    <div class="col-md-8">
      <div class="cell">
      	<h2>Billing History</h2>
      	<p class="lead mb-0">Previous payments by Direct Debit</p>
      	<hr>
        <?=paymentHistory($link, $user)?>
      </div>
      <div class="cell">
        <h2>
          Extra Fees this month
        </h2>
        <p class="lead mb-0">Fees to pay on your next Billing Date, in addition to Squad Fees</p>
        <hr>
      	<?=feesToPay($link, $user)?>
      </div>
    </div>
    <div class="col-md-4">
      <div class="cell">
      	<h2>
          My Bank Account
        </h2>
        <hr>
        <?php if (userHasMandates($user)) {
          $name = strtoupper(bankDetails($user, "account_holder_name"));
          if ($name != "UNKNOWN") {
            $name = $name . ', ';
          } else {
            $name = null;
          }
          $bank = strtoupper(bankDetails($user, "bank_name"));
          $has_logo = false;
          $logo_path = "";

          if ($bank == "TSB BANK PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/tsbbankplc");
          } else if ($bank == "STARLING BANK LIMITED") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/starlingbanklimited");
          } else if ($bank == "LLOYDS BANK PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/lloydsbankplc");
          } else if ($bank == "HALIFAX (A TRADING NAME OF BANK OF SCOTLAND PLC)") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/halifax");
          } else if ($bank == "SANTANDER UK PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/santanderukplc");
          } else if ($bank == "BARCLAYS BANK UK PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/barclaysbankukplc");
          } else if ($bank == "NATIONAL WESTMINSTER BANK PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/nationalwestminsterbankplc");
          } else if ($bank == "HSBC BANK  PLC (RFB)" || $bank == "HSBC UK BANK PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/hsbc");
          } else if ($bank == "THE CO-OPERATIVE BANK PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/coop");
          } else if ($bank == "NATIONWIDE BUILDING SOCIETY") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/nationwide");
          } else if ($bank == "THE ROYAL BANK OF SCOTLAND PLC") {
            $has_logo = true;
            $logo_path = autoUrl("public/img/directdebit/bank-logos/rbs");
          }
          ?>
          <?php if ($has_logo) { ?>
            <img class="img-fluid mb-3" style="max-height:35px;" src="<?=$logo_path?>.png" srcset="<?=$logo_path?>@2x.png 2x, <?=$logo_path?>@3x.png 3x">
          <?php } ?>
          <p class="mb-0"><?=htmlspecialchars($name)?><?=htmlspecialchars(strtoupper(bankDetails($user, "bank_name")))?></p>
          <p class="mono">******<?=htmlspecialchars(strtoupper(bankDetails($user, "account_number_end")))?></p>
          <p><?=CLUB_NAME?> does not store your bank details.</p>
        <?php } ?>
        <p class="mb-0">
        	<a href="<?=autoUrl("payments/setup")?>" class="btn btn-dark btn-block">Add Bank Account</a>
          <?php if (userHasMandates($user)) { ?>
        	<a href="<?=autoUrl("payments/mandates")?>" class="btn btn-dark btn-block">Switch or Manage Bank Account</a>
          <?php } ?>
        </p>
      </div>
      <div class="cell text-white bg-secondary">
        <p class="mb-0">
          <strong>
            We help keep things simple!
          </strong>
        </p>
        <p class="mb-0">
          If you switch your current account through the Current
          Account Switch Service, we'll update your details for you, before
          you even have time to tell us.
        </p>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";
