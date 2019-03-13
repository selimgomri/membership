<?php

$sql = "SELECT * FROM `renewals` ORDER BY `EndDate` DESC LIMIT 5;";
$renewals = mysqli_query($link, $sql);

$sql = "SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() AND CURDATE() <= `EndDate`;";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$use_white_background = true;
$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";
?>

<div class="container">
	<div class="">
	<?php if (mysqli_num_rows($result) > 0) { ?>
		<h1>Membership Renewal for <?php echo $row['Year']; ?></h1>
		<p class="lead">Welcome to the Membership Renewal System for <?php echo
		$row['Year']; ?></p>
		<p>
			Membership renewal ensures all our information about members is up to
			date.
		</p>
		<p>
			The Membership Renewal Period is open until <?php echo date("l j F Y",
			strtotime($row['EndDate'])); ?></p>
		<p>
			We now charge fees by Direct Debit.
		</p>
	<?php } else { ?>
		<h1>Membership Renewal System</h1>
		<p class="lead">Welcome to the Membership Renewal System</p>
		<p>
			Membership renewal ensures all our information about members is up to
			date.
		</p>
		<div class="alert alert-danger">
			<strong>There is no open Renewal Period right now</strong> <br>
			You'll need to add one first
		</div>
	<?php } ?>
	<h2>Previous and Current Renewals</h2>
	<ol>
		<?php for ($i = 0; $i < mysqli_num_rows($renewals); $i++) {
			$renewalArray = mysqli_fetch_array($renewals, MYSQLI_ASSOC);
			?>
			<li>
				<a href="<?php echo autoUrl("renewal/" . $renewalArray['ID']); ?>">
					<?php echo $renewalArray['Name']; ?> (<?php echo date("j F Y",
					strtotime($renewalArray['StartDate'])); ?> - <?php echo date("j F Y",
					strtotime($renewalArray['EndDate'])); ?>)
				</a>
			</li><?
		} ?>
	</ol>
	<h2>Add a new Renewal Period</h2>
	<p class="mb-0">
		<a href="<?php echo autoUrl("renewal/new"); ?>" class="btn
		btn-success">
			Add new Renewal Period
		</a>
	</p>
	</div>
 </div>

<?php include BASE_PATH . "views/footer.php";
