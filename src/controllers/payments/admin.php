<?php

$user = $_SESSION['UserId'];
$pagetitle = "Payments Administration";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

$dateString = date("F Y");

 ?>

 <div class="front-page" style="margin-bottom: -1rem;">
   <div class="container">
  	<h1>Payment Administration</h1>
  	<p class="lead">Control Direct Debit Payments</p>

    <div class="mb-4">
      <h2 class="mb-4">View Squad Fee Status</h2>
      <div class="mb-4">
        <div class="news-grid">

          <a href="<?=autoUrl("payments/history/squads/" . date("Y/m"))?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Squad Fees for <?=$dateString?>
  						</span>
  						<span>
  							View current payment status
  						</span>
  					</span>
            <span class="category">
  						Direct Debit
  					</span>
          </a>

          <a href="<?=autoUrl("payments/history/extras/" . date("Y/m"))?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Extra Fees for <?=$dateString?>
  						</span>
  						<span>
  							View current payment status
  						</span>
  					</span>
            <span class="category">
  						Direct Debit
  					</span>
          </a>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <h2 class="mb-4">Manual Charges</h2>
      <div class="mb-4">
        <div class="news-grid">

          <a href="<?=autoUrl('payments/fees')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Manual Billing Information
  						</span>
  						<span>
  							View all expected fees for all parents
  						</span>
  					</span>
            <span class="category">
  						Pay
  					</span>
          </a>

          <a href="<?=autoUrl('payments/newcharge')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Create a manual charge
  						</span>
  						<span>
  							Charge a parent for a non-automatic fee
  						</span>
  					</span>
            <span class="category">
  						Pay
  					</span>
          </a>

          <a href="<?=autoUrl('payments/galas')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Charge for gala entries
  						</span>
  						<span>
  							After accepted entries are known
  						</span>
  					</span>
            <span class="category">
  						Pay
  					</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
