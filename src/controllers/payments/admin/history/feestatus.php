<?php

$searchDate = mysqli_real_escape_string($link, $year . "-" . $month . "-") . "%";
$name_type = null;

if ($type == "squads") {
	$name_type = "Squad Fees";
} else if ($type == "extras") {
	$name_type = "Extra Fees";
} else {
	halt(404);
}

$sql = "SELECT * FROM `paymentsPending` INNER JOIN `users` ON users.UserID = paymentsPending.UserID WHERE `Date` LIKE '$searchDate' AND `Name` = '$name_type';";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$dateString = date("F Y", strtotime($year . "-" . $month));
$pagetitle = "Status for " . $dateString;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded box-shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Status for <? echo $dateString; ?></h1>
	  <p class="lead">Payments for <? echo $name_type; ?> this Month</p>
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
		<div class="table-responsive">
			<table class="table mb-0">
				<thead class="thead-light">
					<tr>
						<th>
							Parent
						</th>
						<th>
							Swimmers
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
								$data .= '<li>' . $json->Members[$y]->FeeName . " (&pound;" . $json->Members[$y]->Fee . ") for <a href=\"" . autoUrl("swimmers/" . $json->Members[$y]->Member) . "\">" . $json->Members[$y]->MemberName . '</a></li>';
							}
							$data .= '</ul>';
						}
					}
					?>
					<? if ($row['Status'] == "Paid") {
						?><tr class="table-success"><?
					} else if ($row['Status'] == "Failed") {
						?><tr class="table-danger"><?
					} else { ?><tr class=""><?
					} ?>
						<td>
							<? echo $row['Forename'] . " " . $row['Surname']; ?>
						<td>
							<? echo $data; ?>
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
