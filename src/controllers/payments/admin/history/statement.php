<?php

$PaymentID = mysqli_real_escape_string($link, $PaymentID);
$user = $_SESSION['UserID'];

$sql = null;

if ($_SESSION['AccessLevel'] == "Parent") {
	$sql = "SELECT * FROM `paymentsPending` INNER JOIN `users` ON users.UserID = paymentsPending.UserID WHERE `PMkey` = '$PaymentID' AND paymentsPending.UserID = '$user';";
} else {
	$sql = "SELECT * FROM `paymentsPending` INNER JOIN `users` ON users.UserID = paymentsPending.UserID WHERE `PMkey` = '$PaymentID';";
}

$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$pagetitle = "Statement for " . $row['Forename'] . " " . $row['Surname'] . ", "
 . $PaymentID;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded box-shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Statement for <? echo $row['Forename'] . " " . $row['Surname']; ?></h1>
	  <p class="lead">Payment ID: <? echo $PaymentID; ?></p>
		<p>Payments listed below were charged as part of one single Direct Debit</p>
		<div class="table-responsive">
			<table class="table">
				<thead class="thead-light">
					<tr>
						<th>
							Date
						</th>
						<th>
							Description
						</th>
						<th>
							Amount
						</th>
						<th>
							Status
						</th>
					</tr>
				</thead>
				<tbody>
				<?
				for ($i = 0; $i < mysqli_num_rows($result); $i++) {
					?>
					<tr>
						<td>
							<? echo date("d M Y",strtotime($row['Date'])); ?>
						</td>
						<td>
							<? echo $row['Name']; ?>
						</td>
						<td>
							&pound;<? echo number_format(($row['Amount']/100),2,'.',''); ?>
						</td>
						<td>
							<? echo $row['Status']; ?>
						</td>
					</tr>
					<?
					if ($i < mysqli_num_rows($result)-1) {
						$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
					}
				} ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
