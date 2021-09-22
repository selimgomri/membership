<?php

$user = app()->user;
$db = app()->db;

$today = new DateTime('now', new DateTimeZone('Europe/London'));

$getPending = $db->prepare("SELECT membershipBatch.ID id, membershipYear.ID yearId, DueDate due, Total total, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd, PaymentTypes payMethods FROM membershipBatch INNER JOIN membershipYear ON membershipBatch.Year = membershipYear.ID WHERE User = ? AND (DueDate >= ? OR DueDate IS NULL) AND NOT Completed AND NOT Cancelled");
$getPending->execute([
	$user->getId(),
	$today->format('Y-m-d'),
]);

$pagetitle = "Membership Centre";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
	<div class="container-xl">

		<!-- Page header -->
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item active" aria-current="page">Memberships</li>
			</ol>
		</nav>

		<div class="row align-items-center">
			<div class="col-lg-8">
				<h1>
					Membership Centre
				</h1>
				<p class="lead mb-0">
					Welcome to the membership centre
				</p>
			</div>
		</div>
	</div>
</div>

<div class="container-xl">

	<div class="row">
		<div class="col-lg-8">
			<p>
				The Membership Centre lets clubs track which memberships their members hold in a given year.
			</p>

			<h2>Memberships pending payment</h2>

			<?php if ($pending = $getPending->fetch(PDO::FETCH_OBJ)) { ?>

				<p>
					Here are your pending membership fee payments. Please view the batch to review and pay.
				</p>

				<ul class="list-group mb-3">
					<?php do {

						$payMethods = json_decode($pending->payMethods);

					?>
						<li class="list-group-item">

							<h3><?= htmlspecialchars($pending->yearName) ?> <small class="text-muted"><?= htmlspecialchars((new DateTime($pending->yearStart))->format('d/m/Y')) ?> to <?= htmlspecialchars((new DateTime($pending->yearEnd))->format('d/m/Y')) ?></small></h3>

							<dl class="row">

								<dt class="col-3">
									Batch ID
								</dt>
								<dd class="col-9">
									<?= htmlspecialchars($pending->id) ?>
								</dd>

								<dt class="col-3">
									Amount
								</dt>
								<dd class="col-9">
									<?= htmlspecialchars(MoneyHelpers::formatCurrency(MoneyHelpers::intToDecimal($pending->total), 'GBP')) ?>
								</dd>

								<dt class="col-3">
									Pay by
								</dt>
								<dd class="col-9">
									<?php if (sizeof($payMethods) > 0) { ?>
										<ul class="mb-0">
											<?php if (in_array('card', $payMethods)) { ?>
												<li>Credit/debit card</li>
											<?php } ?>
											<?php if (in_array('dd', $payMethods)) { ?>
												<li>Next Direct Debit payment</li>
											<?php } ?>
										<?php } else { ?>
											No payment methods - speak to club staff
										<?php } ?>
								</dd>
							</dl>

							<p class="mb-0">
								<a href="<?= htmlspecialchars(autoUrl("memberships/batches/" . $pending->id)) ?>" class="btn btn-primary">View and pay</a>
							</p>
						</li>
					<?php } while ($pending = $getPending->fetch(PDO::FETCH_OBJ)); ?>
				</ul>
			<?php } else { ?>

				<div class="alert alert-info">
					<p class="mb-0">
						<strong>You have no membership fees to pay</strong>
					</p>
				</div>

			<?php } ?>

			<?php if (app()->user->hasPermission('Admin')) { ?>
				<h2>Membership Years</h2>
				<p>
					<a href="<?= htmlspecialchars(autoUrl('memberships/years')) ?>" class="btn btn-primary">
						Show years/periods
					</a>
				</p>

				<h2>Membership Renewal</h2>
				<p>
					<a href="<?= htmlspecialchars(autoUrl('memberships/renewal')) ?>" class="btn btn-primary">
						View, add or edit renewal periods
					</a>
				</p>

				<h2>Legacy Membership Renewal</h2>

				<p>
					The legacy membership renewal system is deprecated and eventually will be removed.
				</p>

				<p>
					<a href="<?= htmlspecialchars(autoUrl('renewal')) ?>" class="btn btn-primary">
						View legacy renewal periods
					</a>
				</p>
			<?php } ?>
		</div>
	</div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
