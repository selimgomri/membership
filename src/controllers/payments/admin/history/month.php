<?php

if (isset($_GET['status'])) {
	echo "HELLO";
}

$searchDate = mysqli_real_escape_string($link, $year . "-" . $month . "-") . "%";
$sql = "SELECT * FROM `payments` INNER JOIN `users` ON users.UserID = payments.UserID WHERE `Date` LIKE '$searchDate';";
$result = mysqli_query($link, $sql);

$date = strtotime($year . "-" . $month . "-01");

$user = $_SESSION['UserID'];
$pagetitle = date("F Y", $date) . " Payments";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

 ?>

<div class="container">
	<h1>Payments for <? echo date("F Y", $date); ?></h1>
  <p class="lead">All Direct Debit payments requested in <? echo date("F Y", $date); ?></p>
	<? if (mysqli_num_rows($result) > 0) { ?>
		<div class="table-responsive">
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
				for ($i = 0; $i < mysqli_num_rows($result); $i++) {
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
					?>
					<tr>
						<td>
							<? echo $row['Forename'] . " " . $row['Surname']; ?>
						</td>
						<td>
							<abbr title="<? echo $row['PMkey']; ?>"><? echo $row['Name']; ?></abbr>
						</td>
						<td>
							&pound;<? echo number_format(($row['Amount']/100),2,'.',''); ?>
						</td>
						<td>
							<? echo paymentStatusString($row['Status']); ?>
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

<?php include BASE_PATH . "views/footer.php";