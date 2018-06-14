<?php

$user = $_SESSION['UserId'];
$pagetitle = "Gala Payments";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<h1>Payments for Galas</h1>
	<p class="lead">Charge Users for Galas</p>
	<div class="alert alert-info">
		<strong>When using Direct Debit, we charge parents after recieving Accepted Entries</strong> <br>
		This means that there is no need to handle refunds.
	</div>
  <hr>
	<p>List of galas to appear here</p>
</div>

<?php include BASE_PATH . "views/footer.php";
