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

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$name = null;
if (mysqli_num_rows($result) == 0) {
	$sql = "SELECT `UserID` FROM `payments` WHERE `PMkey` = '$PaymentID';";
	$name = getUserName((mysqli_fetch_array(mysqli_query($link, $sql), MYSQLI_ASSOC))['UserID']);
} else {
	$name = $row['Forename'] . " " . $row['Surname'];
}

$pagetitle = "Statement for " . $name . ", "
 . $PaymentID;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Statement for <? echo $name; ?></h1>
	  <p class="lead">Payment ID: <? echo $PaymentID; ?></p>
		<p>Payments listed below were charged as part of one single Direct Debit</p>
		<? if (mysqli_num_rows($result) == 0) { ?>
			<div class="alert alert-warning mb-0">
				<p class="mb-0">
					<strong>
						No fees can be found for this statement
					</strong>
				</p>
				<p class="mb-0">
					This usually means that the payment was created via the GoCardless
					User Interface.
				</p>
			</div>
		<? } else { ?>
		<div class="table-responsive-md">
			<table class="table mb-0">
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
					//$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
					$data = "";
					if ($row['MetadataJSON'] != "" || $row['MetadataJSON'] != "") {
						$json = json_decode($row['MetadataJSON']);
						if ($json->PaymentType == "SquadFees"  || $json->PaymentType == "ExtraFees") {
							$data .= '<ul class="list-unstyled mb-0">';
							//echo sizeof($json->Members);
							//pre($json->Members);
							//echo $json->Members[0]->MemberName;
							$numMems = (int) sizeof($json->Members);
							for ($y = 0; $y < $numMems; $y++) {
								$data .= '<li>' . $json->Members[$y]->FeeName . " for " . $json->Members[$y]->MemberName . '</li>';
							}
							$data .= '</ul>';
						}
					}
					?>
					<tr>
						<td>
							<? echo date("D j M Y",strtotime($row['Date'])); ?>
						</td>
						<td>
							<? echo $row['Name']; ?>
							<em><? echo $data; ?></em>
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
		<? } ?>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
