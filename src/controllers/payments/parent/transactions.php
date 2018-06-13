<?php

$user = $_SESSION['UserID'];
$pagetitle = "Transaction History";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php'; ?>

<div class="container">
	<h1>Transaction History</h1>
	<p class="lead">Previous Payments and Refunds</p>
	<? echo paymentHistory($link, $user); ?>
</div>

<?php

include BASE_PATH . "views/footer.php";
