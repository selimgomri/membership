<?php

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

$db = app()->db;

$searchDate = $year . "-" . $month . "-" . "%";
$getPayments = $db->prepare("SELECT * FROM `payments` INNER JOIN `users` ON users.UserID = payments.UserID WHERE `Date` LIKE ? ORDER BY Forename ASC, Surname ASC");
$getPayments->execute([$searchDate]);

$date = strtotime($year . "-" . $month . "-01");

$use_white_background = true;

$user = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$pagetitle = htmlspecialchars(date("F Y", $date)) . " Payments";

$url = autoUrl("payments/history/" . $year . "/" . $month);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

 ?>

<div class="container">
	<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments/history")?>">History &amp; Status</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars(date("F Y", $date))?></li>
    </ol>
  </nav>
	<div class="">
		<h1>Payments for <?=htmlspecialchars(date("F Y", $date))?></h1>
	  <p class="lead">
      All Direct Debit payments requested in <?=htmlspecialchars(date("F Y", $date))?>
    </p>
		<h2>Payout Reports</h2>
		<p class="lead">For GoCardless payouts made in <?=htmlspecialchars(date("F Y", $date))?>.</p>
		<p>View payments linked to <?=htmlspecialchars(date("F Y", $date))?> payouts from GoCardless to your club's bank account in <a href="<?=htmlspecialchars($url . "/report.csv")?>">CSV</a>, <a href="<?=htmlspecialchars($url . "/report.json")?>">JSON</a> or <a href="<?=htmlspecialchars($url . "/report.pdf")?>">PDF</a> formats.</p>

		<h2>Payment Report</h2>
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
							?><tr class="table-success"><?php
              $link = "text-success";
						} else if ($row['Status'] == "failed" || $row['Status'] == "charged_back") {
							?><tr class="table-danger"><?php
              $link = "text-danger";
						} else if ($row['Status'] == "cust_not_dd") {
							?><tr class="table-warning"><?php
              $link = "text-warning";
						} else {
							?><tr><?php
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
								<a href="<?=htmlspecialchars(autoUrl("payments/statements/" . $row['PaymentID']))?>" title="<?=htmlspecialchars("Statement " . $row['PaymentID'])?>">
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
						<?php
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

<?php $footer = new \SCDS\Footer();
$footer->render();
