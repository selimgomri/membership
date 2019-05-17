<?php

$user = $_SESSION['UserID'];
$pagetitle = "Current Fees";
$use_white_background = true;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php'; ?>

<div class="container">
	<div class="row">
    <div class="col-md-8">
  		<h1 class="">Charges since last bill</h1>
  		<p class="lead">Fees and Charges created since your last bill</p>
  		<p>You'll be billed for these on the first working day of the next month.</p>
    </div>
  </div>
	<?=feesToPay($link, $user)?>
</div>

<?php

include BASE_PATH . "views/footer.php";
