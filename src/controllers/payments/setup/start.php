<?php

$url_path = "payments";
if ($renewal_trap) {
	$url_path = "renewal/payments";
}

$user = $_SESSION['UserID'];
$sql = "SELECT * FROM `paymentSchedule` WHERE `UserID` = '$user';";
$scheduleExists = mysqli_num_rows(mysqli_query($link, $sql));
if ($scheduleExists > 0) {
	$scheduleExists = true;
} else {
	$scheduleExists = false;
}

$pagetitle = "Set up a Direct Debit";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";
 ?>

<div class="container">
	<div class="row">
		<div class="col-lg-6">
			<div class="mb-3 p-3 bg-white rounded shadow">
				<h1>Setup a Direct Debit to pay Chester-le-Street ASC</h1>
				<p class="lead">
					Payments to Chester-le-Street ASC are now moving to direct debit.
				</p>
				<p>
					Direct Debit makes payments simpler for everyone involved. You no
					longer need to pay by standing order or cheque as payments will be
					taken automatically.
				</p>
				<p>
					We'll allow you to choose a day of the month on which to make payment
					requests.
				</p>
				<p class="mb-0"><a href="
					<?
					if ($scheduleExists) {
						echo autoUrl($url_path . "/setup/2");
					} else {
						echo autoUrl($url_path . "/setup/1");
					} ?>
					" class="btn btn-success">Setup a Direct Debit</a>
				</p>
				<? if ($scheduleExists) { ?>
				<p class="small mb-0">
					We'll direct you to our partner GoCardless who handle Direct Debits on
					our behalf.
				</p>
				<? } else { ?>
				<p class="small mb-0">This won't take long.</p>
				<? } ?>
			</div>
		</div>
		<div class="col">
			<div class="mb-3 p-3 bg-white rounded shadow">
				<p class="text-center">
					<img style="max-height:50px;" src="<? echo
					autoUrl("img/directdebit/directdebit.png"); ?>" srcset="<? echo
					autoUrl("img/directdebit/directdebit@2x.png"); ?> 2x, <? echo
					autoUrl("img/directdebit/directdebit@3x.png"); ?> 3x" alt="Direct
					Debit Logo">
				</p>
				<p>
					The Direct Debit Guarantee applies to payments made to
					Chester-le-Street ASC
				</p>
				<ul class="mb-0">
					<li>
						This Guarantee is offered by all banks and building societies that
						accept instructions to pay Direct Debits
					</li>
					<li>
						If there are any changes to the amount, date or frequency of your
						Direct Debit Chester-le-Street ASC will notify you three working
						days in advance of your account being debited or as otherwise
						agreed. If you request Chester-le-Street ASC to collect a payment,
						confirmation of the amount and date will be given to you at the time
						of the request
					</li>
					<li>
						If an error is made in the payment of your Direct Debit, by
						Chester-le-Street ASC or your bank or building society, you are
						entitled to a full and immediate refund of the amount paid from your
						bank or building society
						<ul>
							<li>
								If you receive a refund you are not entitled to, you must pay it
								back when Chester-le-Street ASC asks you to
							</li>
						</ul>
					</li>
					<li>
						You can cancel a Direct Debit at any time by simply contacting your
						bank or building society. Written confirmation may be required.
						Please also notify us.
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
