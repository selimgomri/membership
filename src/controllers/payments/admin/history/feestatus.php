<?php

use Respect\Validation\Validator as v;

if (!v::intVal()->between(1, 12)->validate((int) $month) || !v::stringType()->length(2, 2)->validate($month)) {
	halt(404);
}

if (!v::intVal()->min(1970, true)->validate((int) $year) || !v::stringType()->length(4, null)->validate($year)) {
	halt(404);
}

$searchDate = mysqli_real_escape_string($link, $year . "-" . $month . "-") . "%";
$name_type = null;
$title_string = null;

$dateString = date("F Y", strtotime($year . "-" . $month));

if ($type == "squads") {
	$name_type = "SquadFee";
	$title_string = "Squad Fee payments for " . $dateString;
} else if ($type == "extras") {
	$name_type = "ExtraFee";
	$title_string = "Extra Fee payments for " . $dateString;
} else {
	halt(404);
}

$pagetitle = "Status - " . $dateString;

$sql = "SELECT `Forename`, `Surname`, `MForename`, `MSurname`,
individualFeeTrack.Amount, individualFeeTrack.Description, payments.Status FROM
(((((`individualFeeTrack` INNER JOIN `paymentMonths` ON
individualFeeTrack.MonthID = paymentMonths.MonthID) INNER JOIN `paymentsPending`
ON individualFeeTrack.PaymentID = paymentsPending.PaymentID) INNER JOIN
`members` ON members.MemberID = individualFeeTrack.MemberID) INNER JOIN
`payments` ON paymentsPending.PMkey = payments.PMkey) LEFT JOIN `users` ON
users.UserID = individualFeeTrack.UserID) WHERE `paymentMonths`.`Date` LIKE
'$searchDate' AND `individualFeeTrack`.`Type` = '$name_type' ORDER BY `Forename`
ASC, `Surname` ASC, `users`.`UserID` ASC, `MForename` ASC, `MSurname` ASC;";

//pre($sql);
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded box-shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Status for <? echo $dateString; ?></h1>
	  <p class="lead"><? echo $title_string; ?></p>
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
							Swimmer
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
					?>
					<? if ($row['Status'] == "confirmed" || $row['Status'] == "paid_out") {
						?><tr class="table-success"><?
					} else if ($row['Status'] == "cancelled" || $row['Status'] ==
					"customer_approval_denied" || $row['Status'] == "failed" ||
					$row['Status'] == "charged_back") {
						?><tr class="table-danger"><?
					} else if ($row['Status'] == "cust_not_dd") {
						?><tr class="table-warning"><?
					} else { ?><tr class=""><?
					} ?>
						<td>
							<? echo $row['Forename'] . " " . $row['Surname']; ?>
						<td>
							<ul class="list-unstyled mb-0">
								<li><? echo $row['MForename'] . " " . $row['MSurname']; ?></li>
								<li><em><? echo $row['Description']; ?></em></li>
							</ul>
						</td>
						<td>
							&pound;<? echo number_format(($row['Amount']/100),2,'.',''); ?>
						</td>
						<td>
							<? echo paymentStatusString($row['Status']); ?>
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
