<?php

if (isset($_GET['status'])) {
	echo "HELLO";
}

$searchDate = mysqli_real_escape_string($link, $year . "-" . $month . "-") . "%";
$sql = "SELECT * FROM `payments` INNER JOIN `users` ON users.UserID = payments.UserID WHERE `Date` LIKE '$searchDate';";
$result = mysqli_query($link, $sql);

$date = strtotime($year . "-" . $month . "-01");

$use_white_background = true;

$user = $_SESSION['UserID'];
$pagetitle = date("F Y", $date) . " Payments";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<div class="">
		<h1>Payments for <?php echo date("F Y", $date); ?></h1>
	  <p class="lead">All Direct Debit payments requested in <?php echo date("F Y", $date); ?></p>
		<p>Click on a description for a statement detailing the fees which went into this charge.
			Some payments may not have a statement available</p>
		<?php if (mysqli_num_rows($result) > 0) { ?>
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
					<?
          $link;
					for ($i = 0; $i < mysqli_num_rows($result); $i++) {
						$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
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
								<a href="<?php echo autoUrl("payments/history/statement/" . $row['PMkey']); ?>" title="<?php echo $row['PMkey']; ?>"><?php echo $row['Name']; ?></a>
							</td>
							<td>
								&pound;<?php echo number_format(($row['Amount']/100),2,'.',''); ?>
							</td>
							<td>
								<?php echo paymentStatusString($row['Status']); ?>
							</td>
						</tr>
						<?
					} ?>
					</tbody>
				</table>
			</div> <?
		} else {
			?>
			<div class="alert alert-info">
				<strong>There are no payments to view for this month</strong> <br>
				Please try again later, or check the month you looked for
			</div>
			<?
		} ?>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
