<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payment History";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<h1>Payment History</h1>
  <p class="lead">Select a year and month to view the status of Direct Debit Payments.</p>
</div>

<?php include BASE_PATH . "views/footer.php";
