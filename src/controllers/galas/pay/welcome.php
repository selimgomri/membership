<?php

global $db;

$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
$rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];


try {
$entries = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE members.UserID = ? AND NOT Charged AND FeeToPay > 0 AND galas.GalaDate > CURDATE()");
$entries->execute([$_SESSION['UserID']]);
} catch (Exception $e) {
  pre($e);
}
$entry = $entries->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Pay for entries - Galas";
include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/galas/galaMenu.php";
?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Pay for entries</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-lg-8">
      <h1>Pay for gala entries</h1>
      <p class="lead">You can pay for gala entries by direct debit or by credit or debit card.</p>
      <p>If you haven't opted out of direct debit gala payments and you don't make a payment by card, you'll be automatically charged for gala entries as part of your monthly payment when the gala coordinator submits the entries to the host club.</p>

      <form action="" method="post">
        <?php if ($entry != null) { ?>
        <h2>Select entries to pay for</h2>
        <p>Select which galas you would like to pay for. You can pay for all, some or just one of your gala entries.</p>

        <ul class="list-group mb-3">
					<?php do { ?>
					<?php $notReady = !$entry['EntryProcessed']; ?>
					<li class="list-group-item">
            <h3><?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?> for <?=htmlspecialchars($entry['GalaName'])?></h3>
						<div class="row">
							<div class="col-sm-5 col-md-4 col-lg-6">
								<p class="mb-0">
									<?=htmlspecialchars($entry['MForename'])?> is entered in;
								</p>
								<ul class="list-unstyled">
								<?php $count = 0; ?>
								<?php foreach($swimsArray as $colTitle => $text) { ?>
									<?php if ($entry[$colTitle]) { $count++; ?>
									<li><?=$text?></li>
									<?php } ?>
								<?php } ?>
							</div>
							<div class="col">
								<div class="d-sm-none mb-3"></div>
								<p>
									<?php if ($entry['GalaFeeConstant']) { ?>
									<?=$count?> &times; &pound;<?=htmlspecialchars(number_format($entry['GalaFee'], 2))?>
									<?php } else { ?>
									<strong><?=$count?> entries at no fixed fee.</strong> Please make sure you enter the correct amount or you may face extra charges or may be withdrawn.
									<?php } ?>
								</p>

								<?php if ($notReady) { ?>
								<p>
									This entry has not yet been processed by the gala coordinator. If you pay for this entry now, you'll no longer be able to edit it.
								</p>
								<?php } ?>

                <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" id="<?=$entry['EntryID']?>-pay" name="<?=$entry['EntryID']?>-pay" class="custom-control-input">
                  <label class="custom-control-label" for="<?=$entry['EntryID']?>-pay">Pay for this entry</label>
                </div>
                </div>

								<div class="form-group mb-0">
									<label for="<?=$entry['EntryID']?>-amount">
										Amount to charge
									</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text mono">&pound;</div>
										</div>
                    <input type="number" pattern="[0-9]*([\.,][0-9]*)?" class="form-control mono" id="<?=$entry['EntryID']?>-amount" name="<?=$entry['EntryID']?>-amount" placeholder="0.00" value="<?=htmlspecialchars(number_format($entry['FeeToPay'], 2))?>" min="0" max="150" step="0.01" <?php if ($entry['GalaFeeConstant']) { ?>readonly<?php } ?>>
									</div>
								</div>
							</div>
						</div>
					</li>
					<?php } while ($entry = $entries->fetch(PDO::FETCH_ASSOC)); ?>
				</ul>

        <p>
          <button type="submit" class="btn btn-success">
            Proceed to payment
          </button>
        </p>
        <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>You have no entries to pay for</strong>
          </p>
        </div>
        <?php } ?>
      </form>
    </div>
  </div>
</div>


<?php include BASE_PATH . "views/footer.php"; ?>