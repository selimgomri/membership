<?php

global $db;

$renewals = $db->query("SELECT ID, `Name`, StartDate, EndDate FROM `renewals` ORDER BY `EndDate` DESC LIMIT 5;");

$getRenewals = $db->query("SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() AND CURDATE() <= `EndDate`;");
$row = $getRenewals->fetch(PDO::FETCH_ASSOC);

$use_white_background = true;
$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";
?>

<div class="container">
	<div class="">
		<?php if ($row != null) { ?>
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
		<h2>Recent renewals</h2>
		<ol>
			<?php while ($row = $renewals->fetch(PDO::FETCH_ASSOC)) {
				?>
				<li>
					<a href="<?=autoUrl("renewal/" . $row['ID'])?>">
					<?=htmlspecialchars($row['Name'])?> (<?=date("j F Y",
						strtotime($row['StartDate']))?> - <?=date("j F Y",
						strtotime($row['EndDate']))?>)
					</a>
				</li>
				<?php } ?>
		</ol>
		
		<p>
			<a href="<?php echo autoUrl("renewal/new"); ?>" class="btn
			btn-success">
				Add new Renewal Period
			</a>
		</p>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
