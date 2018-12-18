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

$use_white_background = true;

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
individualFeeTrack.Amount, individualFeeTrack.Description, payments.Status, payments.PaymentID, users.UserID, metadataJSON, individualFeeTrack.MemberID FROM
(((((`individualFeeTrack` LEFT JOIN `paymentMonths` ON
individualFeeTrack.MonthID = paymentMonths.MonthID) LEFT JOIN `paymentsPending`
ON individualFeeTrack.PaymentID = paymentsPending.PaymentID) LEFT JOIN
`members` ON members.MemberID = individualFeeTrack.MemberID) LEFT JOIN
`payments` ON paymentsPending.PMkey = payments.PMkey) LEFT JOIN `users` ON
users.UserID = individualFeeTrack.UserID) WHERE `paymentMonths`.`Date` LIKE
'$searchDate' AND `individualFeeTrack`.`Type` = '$name_type' ORDER BY `Forename`
ASC, `Surname` ASC, `users`.`UserID` ASC, `MForename` ASC, `MSurname` ASC;";

/*
SELECT `Forename`, `Surname`, `MForename`, `MSurname`,
individualFeeTrack.Amount, individualFeeTrack.Description, payments.Status FROM
(((((`individualFeeTrack` LEFT JOIN `paymentMonths` ON
individualFeeTrack.MonthID = paymentMonths.MonthID) LEFT JOIN `paymentsPending`
ON individualFeeTrack.PaymentID = paymentsPending.PaymentID) LEFT JOIN
`members` ON members.MemberID = individualFeeTrack.MemberID) LEFT JOIN
`payments` ON paymentsPending.PMkey = payments.PMkey) LEFT JOIN `users` ON
users.UserID = individualFeeTrack.UserID) WHERE `paymentMonths`.`Date` LIKE
'$searchDate' AND `individualFeeTrack`.`Type` = '$name_type' ORDER BY `Forename`
ASC, `Surname` ASC, `users`.`UserID` ASC, `MForename` ASC, `MSurname` ASC
 */

//pre($sql);
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<h1>Status for <? echo $dateString; ?></h1>
  <p class="lead"><? echo $title_string; ?></p>
	<p><a href="<?=app('request')->curl?>csv" target="_blank">View as CSV (Comma Separated Values)</a> or <a href="<?=app('request')->curl?>json" target="_blank">View as JSON (JavaScript Object Notation)</a></p>
	<? if (mysqli_num_rows($result) == 0) { ?>
		<div class="alert alert-warning mb-0">
			<p class="mb-0">
				<strong>
					No fees can be found for this statement
				</strong>
			</p>
			<p class="mb-0">
				We are sorry for the inconvenience caused.
			</p>
		</div>
	<? } else { ?>
	<div class="table-responsive-md">
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
      $link;
			for ($i = 0; $i < mysqli_num_rows($result); $i++) {
        $metadata = json_decode($row['metadataJSON']);
        $swimmer_name = null;
        for ($j = 0; $j < sizeof($metadata->Members); $j++) {
          if ($metadata->Members[$j]->Member == $row['MemberID']) {
            $swimmer_name = htmlspecialchars($metadata->Members[$j]->MemberName);
          }
        }
				?>
				<? if ($row['Status'] == "confirmed" || $row['Status'] == "paid_out") {
					?><tr class="table-success"><?
          $link = "text-success";
				} else if ($row['Status'] == "cancelled" || $row['Status'] ==
				"customer_approval_denied" || $row['Status'] == "failed" ||
				$row['Status'] == "charged_back" || $row['Status'] == null) {
					?><tr class="table-danger"><?
          $link = "text-danger";
				} else if ($row['Status'] == "cust_not_dd") {
					?><tr class="table-warning"><?
          $link = "text-warning";
				} else { ?><tr class=""><?$link = "";
				} ?>
					<td>
						<? if ($row['Forename'] != null && $row['Surname'] != null) {?>
							<?=htmlspecialchars($row['Forename'] . " " . $row['Surname'])?><br>
              <small><strong>
                <a target="_blank" href="<?=autoUrl("notify/newemail/individual/" . $row['UserID'])?>">
                  Contact Parent
                </a>
              </strong></small>
						<? } else {
							echo "No Parent";
						}?>
					<td>
						<ul class="list-unstyled mb-0">
              <? if ($row['MForename'] == null || $row['MSurname'] == null || $row['MForename'] == "" || $row['MSurname'] == "") { ?>
                <li><?=$swimmer_name?></li>
              <? } else { ?>
                <li><?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?></li>
              <? } ?>
							<li><em><?=htmlspecialchars($row['Description'])?></em></li>
						</ul>
					</td>
					<td>
						&pound;<? echo number_format(($row['Amount']/100),2,'.',''); ?>
					</td>
					<td>
						<? if ($row['Forename'] != null && $row['Surname'] != null) {
							echo paymentStatusString($row['Status']);
						} else {
							echo "No Parent or Direct Debit Available";
						} ?>
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

<?php include BASE_PATH . "views/footer.php";
