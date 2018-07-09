<?php

$sql = "SELECT * FROM `renewals` ORDER BY `EndDate` DESC LIMIT 5;";
$renewals = mysqli_query($link, $sql);

$sql = "SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() <= `EndDate`;";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<? if (mysqli_num_rows($result) > 0) { ?>
		<h1>Membership Renewal for <? echo $row['Year']; ?></h1>
		<p class="lead">Welcome to the Membership Renewal System for <? echo
		$row['Year']; ?></p>
		<p>
			Membership renewal ensures all our information about members is up to
			date.
		</p>
		<p>
			The Membership Renewal Period is open until <? echo date("l j F Y",
			strtotime($row['EndDate'])); ?></p>
		<p>
			We now charge fees by Direct Debit.
		</p>
	<? } else { ?>
		<h1>Membership Renewal for <? echo $row['Year']; ?></h1>
		<p class="lead">Welcome to the Membership Renewal System</p>
		<p>
			Membership renewal ensures all our information about members is up to
			date.
		</p>
		<div class="alert alert-danger">
			<strong>The membership renewal period has not yet started</strong> <br>
			We'll let you know when this starts
		</div>
	<? } ?>
	<h2>Previous and Current Renewals</h2>
	<ol>
		<? for ($i = 0; $i < mysqli_num_rows($renewals); $i++) {
			$renewalArray = mysqli_fetch_array($renewals, MYSQLI_ASSOC);
			?>
			<li>
				<a href="<? echo autoUrl("renewal/" . $renewalArray['ID']); ?>">
					<? echo $renewalArray['Name']; ?> (<? echo date("j F Y",
					strtotime($renewalArray['StartDate'])); ?> - <? echo date("j F Y",
					strtotime($renewalArray['EndDate'])); ?>)
			</li><?
		} ?>
	</ol>
 </div>

<?php include BASE_PATH . "views/footer.php";
