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
	<h1>Payments</h1>
	<p class="lead">Here you can control your Direct Debit details and see your payment history</p>
  <hr>
  <div class="row">
    <div class="col-md-6">
    	<h2>Billing Account Options</h2>
    	<a href="<? echo autoUrl("payments/setup"); ?>" class="btn btn-dark">Add Bank Account</a>
    	<a href="<? echo autoUrl("payments/mandates"); ?>" class="btn btn-dark">Switch Bank Account</a>
    </div>
    <div class="col-md-6">
    	<h2>Billing History</h2>
    	<?php if (mysqli_num_rows($paymentResult) > 0) { ?>
    	<div class="table-responsive">
    		<table class="table table-striped">
    			<thead>
    				<tr>
    					<th>ID</th>
    					<th>Date</th>
    					<th>Amount</th>
    				</tr>
    			</thead>
    			<tbody>
    				<?php for ($i = 0; $i < mysqli_num_rows($paymentResult); $i++) {
    				$row = mysqli_fetch_array($paymentResult, MYSQLI_ASSOC);	?>
    				<tr>
    					<td><? echo $row['PaymentID']; ?></td>
    					<td><? echo $row['Date']; ?></td>
    					<td><? echo $row['Amount']; ?></td>
    				</tr>
    			<?php } ?>
    			</tbody>
    		</table>
    	</div>
	    <?php } else { ?>
    	<div class="alert alert-warning">
    		<strong>You have no previous payments</strong> <br>
    		Payments will appear here when they have been requested
    	</div>
      <?php } ?>
      <h2>Fees this period</h2>
      <p class="lead">Fees to pay on your next Billing Date</p>
    	<?php if (mysqli_num_rows($pendingResult) > 0) { ?>
    	<div class="table-responsive">
    		<table class="table table-striped">
    			<thead>
    				<tr>
    					<th>Description</th>
    					<th>Date</th>
    					<th>Amount</th>
    				</tr>
    			</thead>
    			<tbody>
    				<?php for ($i = 0; $i < mysqli_num_rows($pendingResult); $i++) {
    				$row = mysqli_fetch_array($pendingResult, MYSQLI_ASSOC);	?>
    				<tr>
    					<td><? echo $row['Name']; ?></td>
    					<td><? echo date('j F Y', strtotime($row['Date'])); ?></td>
    					<td>&pound;<? echo number_format(($row['Amount']/100),2,'.',''); ?></td>
    				</tr>
    			<?php } ?>
    			</tbody>
    		</table>
    	</div>
	    <?php } else { ?>
    	<div class="alert alert-warning">
    		<strong>You have no previous payments</strong> <br>
    		Payments will appear here when they have been requested
    	</div>
      <?php } ?>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
