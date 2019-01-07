<?

if ($id > 0) {

	$id = mysqli_real_escape_string($link, $id);

	$sql = "SELECT * FROM `renewals` WHERE `ID` = '$id';";
	$result = mysqli_query($link, $sql);

	if (mysqli_num_rows($result) == 0) {
		halt(404);
	}

	$renewalArray = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$sql = "SELECT * FROM `renewalMembers` WHERE `RenewalID` = '$id';";
	$numRenewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM (`renewalMembers` LEFT JOIN `members` ON
	`members`.`MemberID` = `renewalMembers`.`MemberID`) WHERE `RenewalID` = '$id'
	AND `ASACategory` = '1';";
	$numC1Renewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM (`renewalMembers` LEFT JOIN `members` ON
	`members`.`MemberID` = `renewalMembers`.`MemberID`) WHERE `RenewalID` = '$id'
	AND `ASACategory` = '2';";
	$numC2Renewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM (`renewalMembers` LEFT JOIN `members` ON
	`members`.`MemberID` = `renewalMembers`.`MemberID`) WHERE `RenewalID` = '$id'
	AND `ASACategory` = '3';";
	$numC3Renewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM `members`;";
	$numMembers = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT `MForename`, `MSurname`, `Forename`, `Surname`, `ASANumber`,
	`payments`.`Status`, `RenewalID` FROM ((((`renewalMembers` RIGHT JOIN `members`
	ON members.MemberID = renewalMembers.MemberID) LEFT JOIN `users` ON
	members.UserID = users.UserID) LEFT JOIN `paymentsPending` ON
	renewalMembers.PaymentID = paymentsPending.PaymentID) LEFT JOIN `payments` ON
	payments.PMkey = paymentsPending.PMkey) WHERE (`renewalMembers`.`RenewalID` =
	'$id' OR `renewalMembers`.`RenewalID` IS NULL OR `renewalMembers`.`RenewalID` IS
	NOT NULL) AND (`CountRenewal` = 1 OR `CountRenewal` IS NULL) ORDER BY `MForename` ASC, `MSurname` ASC;";
	$result = mysqli_query($link, $sql);

	if (mysqli_num_rows($result) == 0) {
		halt(404);
	}

	$fluidContainer = true;
  $use_white_background = true;

	$pagetitle = "Membership Renewal";
	include BASE_PATH . "views/header.php";
	include BASE_PATH . "views/swimmersMenu.php";

	?>

	<div class="container-fluid">
		<div class="">
			<h1><? echo $renewalArray['Name']; ?> Status</h1>
			<p class="lead">
				This is the current status for this membership renewal which started on <?
				echo date("l j F Y", strtotime($renewalArray['StartDate'])); ?> and
				finishes on <? echo date("l j F Y", strtotime($renewalArray['EndDate'])); ?>
			</p>
			<p class="mb-0">
				<? echo $numRenewals; ?> Renewals (<? echo $numC1Renewals; ?> Category 1, <?
				echo $numC2Renewals; ?> Category 2, <? echo $numC3Renewals; ?> Category 3)
				of <? echo $numMembers; ?> current* members.
			</p>
			<p class="small text-muted">
				* Current refers to at this moment. There may not have been this number of
				members during this specific membership renewal
			</p>
			<p class="">
				<a href="<? echo autoUrl("renewal/" . $id . "/edit"); ?>" class="btn
				btn-dark">
					Edit this Renewal Period
				</a>
			</p>
		</div>

    <div class="table-responsive-sm">
  		<table class="table">
  			<thead class="thead-light">
  				<tr>
  					<th>
  						Member
  					</th>
  					<th>
  						Parent
  					</th>
  					<th>
  						ASA
  					</th>
  					<th>
  						Payment Status
  					</th>
  				</tr>
  			</thead>
  			<tbody>
  				<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
  					$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  					if ($row['RenewalID'] == "" || $row['RenewalID'] != $id ||
  					$row['Status'] == "failed" || $row['Status'] == "charged_back") {
  						?><tr class="table-danger"><?
  					} else if ($row['Status'] == "paid_out" || $row['Status'] == "confirmed") {
  						?><tr class="table-success"><?
  					} else {
  						?><tr><?
  					}
  					?>
  						<td>
  							<? echo $row['MForename'] . " " . $row['MSurname']; ?>
  						</td>
  						<td>
  							<? echo $row['Forename'] . " " . $row['Surname']; ?>
  						</td>
  						<td>
  							<span class="mono">
  								<? echo $row['ASANumber']; ?>
  							</span>
  						</td>
  						<td>
  							<? if ($row['RenewalID'] == "" || $row['RenewalID'] != $id) {
  								?>No Renewal Exists<?
  							} else if ($row['Status'] == "") {
  								?>Payment not yet processed<?
  							} else {
  								echo paymentStatusString($row['Status']);
  							} ?>
  						</td>
  					</tr>
  					<?
  				} ?>
  			</tbody>
  		</table>
    </div>
	</div>

	<?

	include BASE_PATH . "views/footer.php";
} else {
	$renewalArray = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$sql = "SELECT * FROM `renewalMembers` WHERE `RenewalID` = '0';";
	$numRenewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM (`renewalMembers` LEFT JOIN `members` ON
	`members`.`MemberID` = `renewalMembers`.`MemberID`) WHERE `RenewalID` = '0'
	AND `ASACategory` = '1';";
	$numC1Renewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM (`renewalMembers` LEFT JOIN `members` ON
	`members`.`MemberID` = `renewalMembers`.`MemberID`) WHERE `RenewalID` = '0'
	AND `ASACategory` = '2';";
	$numC2Renewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM (`renewalMembers` LEFT JOIN `members` ON
	`members`.`MemberID` = `renewalMembers`.`MemberID`) WHERE `RenewalID` = '0'
	AND `ASACategory` = '3';";
	$numC3Renewals = mysqli_num_rows(mysqli_query($link, $sql));

	$sql = "SELECT * FROM `members`;";
	$numMembers = mysqli_num_rows(mysqli_query($link, $sql));

	$date = date("Y-m-d", strtotime("first day of -2 month")) . " 00:00:00";

	$sql = "SELECT `MForename`, `MSurname`, `Forename`, `Surname`, `ASANumber`,
	`payments`.`Status`, `RenewalID` FROM ((((`renewalMembers` INNER JOIN `members`
	ON members.MemberID = renewalMembers.MemberID) LEFT JOIN `users` ON
	members.UserID = users.UserID) LEFT JOIN `paymentsPending` ON
	renewalMembers.PaymentID = paymentsPending.PaymentID) LEFT JOIN `payments` ON
	payments.PMkey = paymentsPending.PMkey) WHERE (`renewalMembers`.`RenewalID` =
	'0' OR `renewalMembers`.`RenewalID` IS NULL OR `renewalMembers`.`RenewalID` IS
	NOT NULL) AND `renewalMembers`.`Date` >= '$date' ORDER BY `MForename` ASC, `MSurname` ASC;";
	$result = mysqli_query($link, $sql);

	/*if (mysqli_num_rows($result) == 0) {
		halt(404);
	}*/

	$fluidContainer = true;

	$pagetitle = "Membership Renewal";
	include BASE_PATH . "views/header.php";
	include BASE_PATH . "views/swimmersMenu.php";

	?>

	<div class="container-fluid">
		<div class="my-3 p-3 bg-white rounded shadow">
			<h1>New Member Registration Status</h1>
			<p class="lead">
				This is the current status of online membership registrations since <?=
				date("l j F Y", strtotime("first day of -2 month")) ?>.
			</p>
			<p class="mb-0">
				<? echo $numRenewals; ?> Registrations (<? echo $numC1Renewals; ?>
				Category 1, <? echo $numC2Renewals; ?> Category 2, <? echo
				$numC3Renewals; ?> Category 3).
			</p>
		</div>

		<table class="table bg-white">
			<thead class="thead-light">
				<tr>
					<th>
						Member
					</th>
					<th>
						Parent
					</th>
					<th>
						ASA
					</th>
					<th>
						Payment Status
					</th>
				</tr>
			</thead>
			<tbody>
				<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if ($row['RenewalID'] == "" || $row['RenewalID'] != $id ||
					$row['Status'] == "failed" || $row['Status'] == "charged_back") {
						?><tr class="table-danger"><?
					} else if ($row['Status'] == "paid_out" || $row['Status'] == "confirmed") {
						?><tr class="table-success"><?
					} else {
						?><tr><?
					}
					?>
						<td>
							<? echo $row['MForename'] . " " . $row['MSurname']; ?>
						</td>
						<td>
							<? echo $row['Forename'] . " " . $row['Surname']; ?>
						</td>
						<td>
							<span class="mono">
								<? echo $row['ASANumber']; ?>
							</span>
						</td>
						<td>
							<? if ($row['RenewalID'] == "" || $row['RenewalID'] != $id) {
								?>No Renewal Exists<?
							} else if ($row['Status'] == "") {
								?>Payment not yet processed<?
							} else {
								echo paymentStatusString($row['Status']);
							} ?>
						</td>
					</tr>
					<?
				} ?>
			</tbody>
		</table>
	</div>

	<?

	include BASE_PATH . "views/footer.php";
}
