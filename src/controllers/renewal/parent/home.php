<?php

$sql = "SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() AND CURDATE() <= `EndDate`;";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$year = date("Y", strtotime('+1 year'));

$pagetitle = $year . " Membership Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<? if (mysqli_num_rows($result) > 0) { ?>
		<h1>Membership Renewal for <? echo $row['Year']; ?></h1>
		<p class="lead">Welcome to the Membership Renewal System for <? echo
		$row['Year']; ?></p>
		<p>Membership renewal ensures all our information about you is up to date,
			that you and your swimmers understand your rights and responsibilities at
			the club, and that you can pay your <abbr title="Including ASA Fees">
			membership fee</abbr> for the year ahead.
		</p>
		<p>
			The Membership Renewal Period is open until <? echo date("l j F Y",
			strtotime($row['EndDate'])); ?></p>
		<p>This year, we'll be charging you for your Membership Fees through your
			direct debit. This will be as an add-on to your usual fees.
		</p>
		<p>We'll save your progress as you fill out the required forms.</p>
		<p>
			<a class="btn btn-success" href="<? echo autoUrl("renewal/go"); ?>">Get
			Started</a>
		</p>
	<? } else { ?>
		<h1>Membership Renewal for <?=$year?></h1>
		<p class="lead">Welcome to the Membership Renewal System</p>
		<p>Membership renewal ensures all our information about you is up to date,
			that you and your swimmers understand your rights and responsibilities at
			the club, and that you can pay your <abbr title="Including ASA Fees">
			membership fee</abbr> for the year ahead.
		</p>
		<div class="alert alert-danger">
			<strong>The membership renewal period for <?=$year?> has not yet
			started</strong> <br> We'll let you know when this starts
		</div>
	<? } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
