<?php

global $db;

$searchDate = $year . "-" . $month . "-" . "%";
$getPayments = $db->prepare("SELECT * FROM `payments` INNER JOIN `users` ON users.UserID = payments.UserID WHERE `Date` LIKE ?");
$getPayments->execute([$searchDate]);

$date = strtotime($year . "-" . $month . "-01");

$use_white_background = true;

$user = $_SESSION['UserID'];
$pagetitle = htmlspecialchars(date("F Y", $date)) . " Payments";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<div class="">
		<h1>Payments for <?=htmlspecialchars(date("F Y", $date))?></h1>
	  <p class="lead">
      All Direct Debit payments requested in <?=htmlspecialchars(date("F Y", $date))?>
    </p>
		<p>Click on a description for a statement detailing the fees which went into this charge.
			Some payments may not have a statement available</p>
		<?php
    $row = $getPayments->fetch(PDO::FETCH_ASSOC);

    if ($row != null) { ?>
			<div class="table-responsive-md">
				<table class="table">
					<thead class="thead-light">
						<tr>
							<th>
								User
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
					<?php
					do {
						if ($row['Status'] == "confirmed" || $row['Status'] == "paid_out" || $row['Status'] == "paid_manually") {
							?><tr class="table-success"><?
              $link = "text-success";
						} else if ($row['Status'] == "failed" || $row['Status'] == "charged_back") {
							?><tr class="table-danger"><?
              $link = "text-danger";
						} else if ($row['Status'] == "cust_not_dd") {
							?><tr class="table-warning"><?
              $link = "text-warning";
						} else {
							?><tr><?
						}?>
							<td>
                <?=htmlspecialchars($row['Forename'] . " " . $row['Surname'])?><br>
                <small>
                  <a target="_blank" href="<?=autoUrl("notify/newemail/individual/" . $row['UserID'])?>">
                    Contact Parent
                  </a>
                </small>
							</td>
							<td>
								<a href="<?=autoUrl("payments/history/statement/" . htmlspecialchars($row['PMkey']))?>" title="<?=htmlspecialchars($row['PMkey'])?>">
                  <?=htmlspecialchars($row['Name'])?>
                </a>
							</td>
							<td>
								&pound;<?=htmlspecialchars(number_format(($row['Amount']/100),2,'.',''))?>
							</td>
							<td>
								<?=htmlspecialchars(paymentStatusString($row['Status']))?>
							</td>
						</tr>
						<?
					} while ($row = $getPayments->fetch(PDO::FETCH_ASSOC)); ?>
					</tbody>
				</table>
			</div> <?php
		} else {
			?>
			<div class="alert alert-info">
				<strong>There are no payments to view for this month</strong> <br>
				Please try again later, or check the month you looked for
			</div>
			<?php
		} ?>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
