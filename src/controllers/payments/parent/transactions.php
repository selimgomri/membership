<?php

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

$user = $_SESSION['UserID'];
$pagetitle = "Transaction History";

$use_white_background = true;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

?>

<div class="container">
	<div class="">
		<h1 class="">Transaction History</h1>
		<p class="lead">Previous Payments and Refunds</p>
		<?=paymentHistory(null, $user)?>
	</div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
